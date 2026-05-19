<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportDummyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-dummy-users
                            {--count=300 : How many dummy users to create}
                            {--days=120 : Spread created_at across the last N days}
                            {--password=password : Plain password assigned to every dummy user}
                            {--fresh : Delete previously created non-admin users before importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a batch of dummy customer users for testing dashboards, orders, etc.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count    = max(0, (int) $this->option('count'));
        $days     = max(1, (int) $this->option('days'));
        $password = (string) $this->option('password');
        $fresh    = (bool) $this->option('fresh');

        $this->info("Importing {$count} dummy users across the last {$days} days");
        Log::info('ImportDummyUsers started', compact('count', 'days', 'fresh'));

        if ($fresh) {
            $deleted = $this->purgeNonAdminUsers();
            $this->warn("Removed {$deleted} previously created non-admin users.");
        }

        if ($count === 0) {
            $this->info('Nothing to import (count=0).');
            return self::SUCCESS;
        }

        $faker          = FakerFactory::create();
        $hashedPassword = Hash::make($password);
        $now            = Carbon::now();

        $existingEmails = User::query()->pluck('email')->map(fn ($e) => strtolower($e))->all();
        $existingEmails = array_flip($existingEmails);

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $createdCount = 0;
        $batch        = [];

        try {
            DB::transaction(function () use ($count, $days, $faker, $hashedPassword, $now, &$existingEmails, $bar, &$createdCount, &$batch) {
                for ($i = 0; $i < $count; $i++) {
                    $firstName = $faker->firstName();
                    $lastName  = $faker->lastName();
                    $name      = $firstName . ' ' . $lastName;

                    $email = $this->uniqueEmail($faker, $firstName, $lastName, $existingEmails);
                    $existingEmails[strtolower($email)] = true;

                    $createdAt = $this->randomCreatedAt($now, $days);

                    $batch[] = [
                        'name'              => $name,
                        'email'             => $email,
                        'email_verified_at' => $faker->boolean(80) ? $createdAt : null,
                        'password'          => $hashedPassword,
                        'remember_token'    => Str::random(10),
                        'created_at'        => $createdAt,
                        'updated_at'        => $createdAt,
                    ];

                    if (count($batch) >= 500) {
                        DB::table('users')->insert($batch);
                        $createdCount += count($batch);
                        $bar->advance(count($batch));
                        $batch = [];
                    }
                }

                if (!empty($batch)) {
                    DB::table('users')->insert($batch);
                    $createdCount += count($batch);
                    $bar->advance(count($batch));
                    $batch = [];
                }
            });
        } catch (\Throwable $e) {
            $bar->finish();
            $this->newLine(2);
            $this->error('Error importing dummy users: ' . $e->getMessage());
            Log::error('ImportDummyUsers failed', ['exception' => $e]);
            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully imported {$createdCount} dummy users (password: \"{$password}\").");

        return self::SUCCESS;
    }

    /**
     * Delete all users that are NOT attached to the Administrator role,
     * including their order history (cascades), so we can start clean.
     */
    protected function purgeNonAdminUsers(): int
    {
        $adminRoleId = Role::query()->where('name', 'Administrator')->value('id');

        $adminUserIds = $adminRoleId
            ? DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id')->all()
            : [];

        return User::query()
            ->when(!empty($adminUserIds), fn ($q) => $q->whereNotIn('id', $adminUserIds))
            ->delete();
    }

    /**
     * Generate an email that doesn't collide with existing users in this batch
     * or in the database.
     *
     * @param array<string, bool> $existingEmails  map of lowercase email => true
     */
    protected function uniqueEmail($faker, string $firstName, string $lastName, array $existingEmails): string
    {
        $base = Str::slug($firstName . '.' . $lastName, '.');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = $base . ($attempt === 0 ? '' : $attempt) . '@' . $faker->safeEmailDomain();
            if (!isset($existingEmails[strtolower($candidate)])) {
                return $candidate;
            }
        }

        return Str::random(12) . '@' . $faker->safeEmailDomain();
    }

    /**
     * Pick a random created_at biased toward more recent days so the
     * dashboard's customer growth comparison shows interesting deltas.
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
}
