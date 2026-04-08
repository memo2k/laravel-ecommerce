<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Permissions
        $permissions = [
            'view products',
            'manage products',
            'view orders',
            'manage orders',
            'view users',
            'manage users',
            'view roles',
            'manage roles',
            'view permissions',
            'manage permissions',
            'view categories',
            'manage categories',
            'view reports',
            'manage reports',
            'view settings',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'description' => $permission,
            ]);
        }
        
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@site.com',
            'password' => Hash::make('Pl42@sa!'),
        ]);

        Role::create([
            'name' => 'Administrator',
            'is_active' => true,
        ]);

        
        $adminRole = Role::where('name', 'Administrator')->first();
        $adminUser = User::where('email', 'admin@site.com')->first();
        
        $adminUser->roles()->attach($adminRole);
        
        $permissions = Permission::all();
        $adminRole->permissions()->attach($permissions);

        // Categories
        $categories = [
            'Electronics',
            'Clothing',
            'Home',
            'Sports',
            'Toys',
            'Jewelry',
            'Books',
            'Music',
            'Art',
            'Collectibles',
        ];
        
        foreach ($categories as $category) {
            ProductCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => $category,
            ]);
        }


        // Products
        $products = [
            'Product 1',
            'Product 2',
            'Product 3',
            'Product 4',
            'Product 5',
            'Product 6',
        ];

        foreach ($products as $product) {
            Product::create([
                'product_category_id' => 1,
                'name' => $product,
                'slug' => Str::slug($product),
                'description' => $product,
                'price' => 100,
                'stock' => 100,
            ]);
        }

        // Orders
        $orders = [
            'Order 1',
            'Order 2',
            'Order 3',
            'Order 4',
            'Order 5',
            'Order 6',
        ];
        foreach ($orders as $order) {
            Order::create([
                'user_id' => 1,
                'total_amount' => 100,
                'status' => 'Pending',
                'payment_method' => 'Cash',
                'delivery_address' => '123 Main St, Anytown, USA',
                'city' => 'Anytown',
                'state' => 'CA',
                'zip' => '12345',
                'country' => 'USA',
                'customer_phone' => '1234567890',
                'customer_email' => 'test@test.com',
                'customer_first_name' => 'John',
                'customer_last_name' => 'Doe',
                'customer_notes' => 'Test notes',
            ]);
        }

        // Order Products
        $orderProducts = [
            'Order Product 1',
            'Order Product 2',
            'Order Product 3',
            'Order Product 4',
            'Order Product 5',
            'Order Product 6',
        ];
        foreach ($orderProducts as $orderProduct) {
            OrderProduct::create([
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price' => 100,
                'total' => 100,
            ]);
        }
    }
}
