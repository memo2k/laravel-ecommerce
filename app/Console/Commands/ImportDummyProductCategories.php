<?php

namespace App\Console\Commands;

use App\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportDummyProductCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-dummy-product-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dummy product categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Importing dummy product categories from dummyjson.com');
        Log::info('Importing dummy product categories from dummyjson.com');
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('product_categories')->truncate();
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        try {
            $categories = ['laptops', 'smartphones', 'mobile-accessories', 'tablets', 'mens-watches', 'womens-watches'];

            $categoriesToImport = [];

            foreach($categories as $category) {
                $categoriesToImport[] = [
                    'slug' => Str::slug($category),
                    'name' => ucfirst($category),
                    'description' => ucfirst($category),
                ];
            }

            ProductCategory::insert($categoriesToImport);

            $this->info('Dummy product categories imported successfully');
        } catch (\Throwable $e) {
            $this->error('Error importing dummy product categories: ' . $e->getMessage());
            Log::error('Error importing dummy product categories: ' . $e);
        }
    }
}
