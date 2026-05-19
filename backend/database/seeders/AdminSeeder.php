<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Creates the default admin and one user per role for testing.
     * Run with: php artisan db:seed --class=AdminSeeder
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Dranreb Arzadon',
                'email'    => 'admin@bluelotus.com',
                'password' => Hash::make('admin123'),
                'role'     => 'Admin',
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Default users seeded successfully.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            collect($users)->map(fn($u) => [
                $u['role'],
                $u['email'],

                match($u['role']) {
                    'Admin'      => 'admin123',
                    'HR'         => 'hr123',
                    'Accountant' => 'acc123',
                    default      => 'employee123',
                }
            ])->toArray()
        );
    }
}