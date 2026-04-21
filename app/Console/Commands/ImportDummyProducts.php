<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportDummyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-dummy-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dummy products from dummyjson.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing dummy products from dummyjson.com');
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('products')->truncate();
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        try {
            $products = Http::get('https://dummyjson.com/products?limit=0')->json()['products'];
            $categories = ProductCategory::all()->pluck('slug', 'id')->toArray();

            $productsToImport = [];

            foreach($products as $product) {
                if(in_array($product['category'], $categories)) {
                    $slug = Str::slug($product['title']);
                    $imagePath = $this->downloadImage($product['images'][0] ?? null, $slug);
                    $productCategoryId = array_search($product['category'], $categories);

                    $productsToImport[] = [
                        'product_category_id' => $productCategoryId,
                        'sku' => $product['sku'],
                        'name' => $product['title'],
                        'slug' => $slug,
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'stock' => $product['stock'],
                        'image' => $imagePath,
                    ];
                }
            }

            Product::insertOrIgnore($productsToImport);

            $this->info('Dummy products imported successfully');
        } catch (\Throwable $e) {
            $this->error('Error importing dummy products: '.$e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function downloadImage(?string $url, string $slug): ?string
    {
        if (empty($url)) {
            return null;
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $slug.'.'.$extension;
        $storagePath = 'products/'.$filename;

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                $this->warn("Failed to download image for {$slug}: HTTP {$response->status()}");
                return null;
            }

            Storage::disk('public')->put($storagePath, $response->body());
        } catch (\Throwable $e) {
            $this->warn("Failed to download image for {$slug}: ".$e->getMessage());
            return null;
        }

        return $storagePath;
    }
}
