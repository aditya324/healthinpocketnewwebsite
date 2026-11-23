<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Reporter;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->where('email', 'admin@sunrise.test')->delete();

        Admin::query()->firstOrCreate(
            ['email' => 'admin@sunrise.test'],
            [
                'name' => 'Sunrise Admin',
                'password' => 'password',
            ],
        );

        Reporter::query()->firstOrCreate(
            ['email' => 'reports@sunrise.test'],
            [
                'name' => 'Weekly Reports',
                'password' => 'password',
            ],
        );
    }
}
