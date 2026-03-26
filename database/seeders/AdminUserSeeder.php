<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifie s'il existe déjà un user avec role = admin
        $existingAdmin = User::where('role', 'admin')->first();

        if ($existingAdmin) {
            $this->command->info('Un admin existe déjà : ' . $existingAdmin->email);
            return;
        }

        User::create([
            'name'     => 'Admin Local',
            'email'    => 'admin@local.dev',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        $this->command->info('Admin créé : admin@local.dev / password123');
    }
}