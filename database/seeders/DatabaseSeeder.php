<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Tony',
            'email' => 'nguyenvancuong@honghafeed.com.vn',
            'password' => bcrypt('Hongha@123'),
            'is_disabled' => false,
            'is_admin' => true,
        ]);
    }
}
