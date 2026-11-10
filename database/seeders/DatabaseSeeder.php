<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'velunastoree@gmail.com'],
            [
                'name' => 'Veluna Store',
                'password' => 'veluna0335',
                'email_verified_at' => now(),
                'role' => 'admin',
                'is_active' => true,
            ],
        );
    }
}
