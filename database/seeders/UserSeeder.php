<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@student.nemsu.edu.ph', 'role' => 'student'],
            ['name' => 'Ana Faculty', 'email' => 'ana@faculty.nemsu.edu.ph', 'role' => 'faculty'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'status' => 'active',
                    'phone' => '09123456789',
                ]
            );
        }
    }
}
