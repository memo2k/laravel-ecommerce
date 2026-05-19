<?php

namespace App\Console\Commands;

use App\Constants\OrderStatusConstant;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportDummyOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-dummy-orders
                            {--count=800 : How many dummy orders to create}
                            {--days=120 : Spread orders across the last N days}
                            {--max-product-id=48 : Only use products with id <= this value}
                            {--users-if-missing=300 : If no non-admin users exist, auto-import this many first}
                            {--guest-rate=15 : Percent chance an order is placed by a guest (no user_id)}
                            {--fresh : Truncate orders & order_products before importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a large batch of dummy orders (with line items) so the admin dashboard analytics has data to render';

    /**
     * Order statuses with weights – heavier on "Delivered" so KPIs look healthy.
     * Values come from {@see OrderStatusConstant} so they stay in sync.
     *
     * @return array<string, int>
     */
    protected function statusWeights(): array
    {
        return [
            OrderStatusConstant::DELIVERED  => 55,
            OrderStatusConstant::SHIPPED    => 15,
            OrderStatusConstant::PROCESSING => 12,
            OrderStatusConstant::PENDING    => 10,
            OrderStatusConstant::UNPAID     => 3,
            OrderStatusConstant::CANCELLED  => 5,
        ];
    }

    /**
     * @var array<string, int>
     */
    protected array $paymentMethodWeights = [
        'credit_card'      => 50,
        'paypal'           => 20,
        'stripe'           => 15,
        'bank_transfer'    => 8,
        'cash_on_delivery' => 7,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count          = (int) $this->option('count');
        $days           = max(1, (int) $this->option('days'));
        $maxProductId   = (int) $this->option('max-product-id');
        $usersIfMissing = (int) $this->option('users-if-missing');
        $guestRate      = max(0, min(100, (int) $this->option('guest-rate')));
        $fresh          = (bool) $this->option('fresh');

        $this->info("Importing {$count} dummy orders across the last {$days} days (products 1..{$maxProductId})");
        Log::info('ImportDummyOrders started', compact('count', 'days', 'maxProductId', 'guestRate', 'fresh'));

        $products = Product::query()
            ->where('id', '<=', $maxProductId)
            ->get(['id', 'price', 'discount_price'])
            ->keyBy('id');

        if ($products->isEmpty()) {
            $this->error("No products found with id <= {$maxProductId}. Seed products first.");
            return self::FAILURE;
        }

        $customers = $this->loadCustomers();

        if ($customers->isEmpty() && $usersIfMissing > 0) {
            $this->warn("No non-admin users found — running app:import-dummy-users --count={$usersIfMissing} first.");
            Artisan::call('app:import-dummy-users', ['--count' => $usersIfMissing], $this->output);
            $customers = $this->loadCustomers();
        }

        if ($customers->isEmpty()) {
            $this->warn('Still no customer users available — all orders will be created as guest checkouts.');
        } else {
            $this->info("Using {$customers->count()} customer users (excluding administrators).");
        }

        $customerIds = $customers->keys()->all();

        if ($fresh) {
            $this->warn('Truncating orders and order_products...');
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('order_products')->truncate();
            DB::table('orders')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }

        $faker = FakerFactory::create();

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $created = 0;

        try {
            DB::transaction(function () use ($count, $days, $products, $customers, $customerIds, $guestRate, $faker, $bar, &$created) {
                $lineItemBuffer = [];
                $now = Carbon::now();

                for ($i = 0; $i < $count; $i++) {
                    $createdAt = $this->randomCreatedAt($now, $days);

                    $lineItems = $this->buildLineItems($products, $faker);

                    $productsTotal = array_sum(array_column($lineItems, 'total'));
                    $shipping      = $this->randomShipping($faker, $productsTotal);
                    $total         = round($productsTotal + $shipping, 2);

                    $isGuest = empty($customerIds) || $faker->boolean($guestRate);
                    $user    = $isGuest ? null : $customers[$customerIds[array_rand($customerIds)]];

                    [$firstName, $lastName] = $this->splitName($user?->name, $faker);
                    $email = $user?->email ?? $faker->safeEmail();

                    $orderId = DB::table('orders')->insertGetId([
                        'user_id'              => $user?->id,
                        'products_total_amount'=> round($productsTotal, 2),
                        'shipping_amount'      => $shipping,
                        'total_amount'         => $total,
                        'status'               => $this->weightedPick($this->statusWeights(), $faker),
                        'payment_method'       => $this->weightedPick($this->paymentMethodWeights, $faker),
                        'delivery_address'     => $faker->streetAddress(),
                        'city'                 => $faker->city(),
                        'state'                => $faker->state(),
                        'zip'                  => $faker->postcode(),
                        'country'              => $faker->country(),
                        'customer_phone'       => $faker->phoneNumber(),
                        'customer_email'       => $email,
                        'customer_first_name'  => $firstName,
                        'customer_last_name'   => $lastName,
                        'customer_notes'       => $faker->boolean(15) ? $faker->sentence() : null,
                        'created_at'           => $createdAt,
                        'updated_at'           => $createdAt,
                    ]);

                    foreach ($lineItems as $item) {
                        $item['order_id']   = $orderId;
                        $item['created_at'] = $createdAt;
                        $item['updated_at'] = $createdAt;
                        $lineItemBuffer[] = $item;
                    }

                    $created++;

                    if ($created % 200 === 0) {
                        $this->flushLineItems($lineItemBuffer);
                        $bar->advance(200);
                    }
                }

                if (!empty($lineItemBuffer)) {
                    $this->flushLineItems($lineItemBuffer);
                }
                $bar->setProgress($created);
            });
        } catch (\Throwable $e) {
            $bar->finish();
            $this->newLine(2);
            $this->error('Error importing dummy orders: ' . $e->getMessage());
            Log::error('ImportDummyOrders failed', ['exception' => $e]);
            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully imported {$created} dummy orders.");

        return self::SUCCESS;
    }

    /**
     * Load non-admin users keyed by id. These are the "real customers" we
     * attach orders to so the dashboard's customer count + orders correlate.
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\User>
     */
    protected function loadCustomers()
    {
        $adminRoleId = Role::query()->where('name', 'Administrator')->value('id');

        $adminUserIds = $adminRoleId
            ? DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id')->all()
            : [];

        return User::query()
            ->when(!empty($adminUserIds), fn ($q) => $q->whereNotIn('id', $adminUserIds))
            ->get(['id', 'name', 'email'])
            ->keyBy('id');
    }

    /**
     * Split a "First Last" name into [first, last]. Falls back to faker when
     * the user is a guest or has only one token in their name.
     *
     * @return array{0:string,1:string}
     */
    protected function splitName(?string $fullName, $faker): array
    {
        if (!$fullName) {
            return [$faker->firstName(), $faker->lastName()];
        }

        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $first = array_shift($parts) ?: $faker->firstName();
        $last  = !empty($parts) ? implode(' ', $parts) : $faker->lastName();

        return [$first, $last];
    }

    /**
     * Flush buffered line items in chunks and reset the buffer.
     *
     * @param array<int, array<string, mixed>> $lineItemBuffer
     */
    protected function flushLineItems(array &$lineItemBuffer): void
    {
        foreach (array_chunk($lineItemBuffer, 500) as $chunk) {
            DB::table('order_products')->insert($chunk);
        }
        $lineItemBuffer = [];
    }

    /**
     * Build 1..5 line items for a single order.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Product> $products
     * @return array<int, array{product_id:int, quantity:int, price:float, total:float}>
     */
    protected function buildLineItems($products, $faker): array
    {
        $lineItemCount = $faker->numberBetween(1, 5);

        $picked = $products->random(min($lineItemCount, $products->count()));
        if (!is_iterable($picked)) {
            $picked = [$picked];
        }

        $items = [];
        foreach ($picked as $product) {
            $quantity   = $faker->numberBetween(1, 4);
            $unitPrice  = (float) ($product->discount_price > 0 ? $product->discount_price : $product->price);
            $unitPrice  = round($unitPrice, 2);

            $items[] = [
                'product_id' => $product->id,
                'quantity'   => $quantity,
                'price'      => $unitPrice,
                'total'      => round($unitPrice * $quantity, 2),
            ];
        }

        return $items;
    }

    /**
     * Pick a random created_at biased towards more recent days so the trend line
     * looks like a growing business.
     */
    protected function randomCreatedAt(Carbon $now, int $days): Carbon
    {
        $daysAgo = (int) floor(($days - 1) * (1 - sqrt(mt_rand() / mt_getrandmax())));
        $daysAgo = max(0, min($days - 1, $daysAgo));

        return $now->copy()
            ->subDays($daysAgo)
            ->setTime(
                mt_rand(8, 22),
                mt_rand(0, 59),
                mt_rand(0, 59),
            );
    }

    /**
     * Shipping is a small flat-ish fee, occasionally free for big baskets.
     */
    protected function randomShipping($faker, float $productsTotal): float
    {
        if ($productsTotal > 500 && $faker->boolean(60)) {
            return 0.00;
        }

        return (float) $faker->randomElement([4.99, 7.99, 9.99, 12.99, 14.99, 19.99]);
    }

    /**
     * Pick a key from [value => weight] using the given Faker instance.
     *
     * @param array<string, int> $weights
     */
    protected function weightedPick(array $weights, $faker): string
    {
        $total = array_sum($weights);
        $roll  = $faker->numberBetween(1, $total);
        $acc   = 0;
        foreach ($weights as $value => $weight) {
            $acc += $weight;
            if ($roll <= $acc) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}
