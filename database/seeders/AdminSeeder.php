<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@nemsu.edu.ph');
        $password = env('ADMIN_PASSWORD', 'password');

        $admin = User::where('email', $email)->first();

        if (!$admin) {
            User::create([
                'name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'status' => 'active',
                'department' => 'Administration',
                'phone' => '09999999999',
            ]);
        } else {
            $admin->update([
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make($password),
            ]);
        }
    }
}
