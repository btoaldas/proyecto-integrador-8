<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Administrador Municipal',
                'email' => 'admin@municipio.gov',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Secretario Municipal',
                'email' => 'secretario@municipio.gov',
                'password' => Hash::make('secretario123'),
                'role' => User::ROLE_SECRETARY,
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Revisor de Documentos',
                'email' => 'revisor@municipio.gov',
                'password' => Hash::make('revisor123'),
                'role' => User::ROLE_REVIEWER,
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Ciudadano Público',
                'email' => 'publico@municipio.gov',
                'password' => Hash::make('publico123'),
                'role' => User::ROLE_VIEWER,
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Test users created successfully!');
        $this->command->line('Login credentials:');
        $this->command->line('Admin: admin@municipio.gov / admin123');
        $this->command->line('Secretary: secretario@municipio.gov / secretario123');
        $this->command->line('Reviewer: revisor@municipio.gov / revisor123');
        $this->command->line('Viewer: publico@municipio.gov / publico123');
    }
}