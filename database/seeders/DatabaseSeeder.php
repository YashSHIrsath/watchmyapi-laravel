<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Company
        $company = Company::create([
            'name' => 'Acme Corp',
            'status' => 'active',
        ]);

        // 2. Create Super Admin
        User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Super Admin',
            'email' => 'admin@watchmyapi.com',
            'password' => Hash::make('password123'),
            'user_type' => 'super_admin',
            'company_id' => null,
            'is_active' => true,
        ]);

        // 3. Create Company User
        User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Acme User',
            'email' => 'user@acme.com',
            'password' => Hash::make('password123'),
            'user_type' => 'company_user',
            'company_id' => $company->id,
            'is_active' => true,
        ]);
    }
}
