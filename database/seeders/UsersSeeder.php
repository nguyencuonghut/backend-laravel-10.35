<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\User::factory(100)->create();

        \App\Models\User::factory()->create([
            'name' => 'Tony',
            'email' => 'nguyenvancuong@honghafeed.com.vn',
            'password' => bcrypt('Hongha@123'),
            'role_id' => 1,
            'status_id' => 1,
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Trang Gà',
            'email' => 'trang@honghafeed.com.vn',
            'password' => bcrypt('Hongha@123'),
            'role_id' => 2,
            'status_id' => 1,
        ]);
    }
}
