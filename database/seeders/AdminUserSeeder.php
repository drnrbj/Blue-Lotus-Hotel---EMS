<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@bluelotus.com',
            'password' => bcrypt('password123'),
            'role'     => 'hr',
        ]);
    }
}