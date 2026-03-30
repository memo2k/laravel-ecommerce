<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
    }
}
