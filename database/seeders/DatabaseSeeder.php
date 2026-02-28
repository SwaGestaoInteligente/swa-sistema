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
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@swa-mobile.com')],
            [
                'name' => env('ADMIN_NAME', 'Administrador SWA'),
                'password' => env('ADMIN_PASSWORD', 'Swa@123456'),
                'email_verified_at' => now(),
            ]
        );
    }
}
