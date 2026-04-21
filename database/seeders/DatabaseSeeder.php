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
use Illuminate\Support\Facades\Artisan;
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

        Artisan::call('app:import-dummy-product-categories');
        Artisan::call('app:import-dummy-products');
    }
}
