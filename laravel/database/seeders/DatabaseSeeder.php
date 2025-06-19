<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'id' => Str::uuid(),
            'name' => 'Administrador Municipal',
            'email' => 'admin@municipal.gov',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create secretary user
        User::create([
            'id' => Str::uuid(),
            'name' => 'Secretario Municipal',
            'email' => 'secretario@municipal.gov',
            'password' => Hash::make('secretario123'),
            'role' => 'secretary',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create reviewer user
        User::create([
            'id' => Str::uuid(),
            'name' => 'Revisor de Documentos',
            'email' => 'revisor@municipal.gov',
            'password' => Hash::make('revisor123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create viewer user
        User::create([
            'id' => Str::uuid(),
            'name' => 'Usuario Visualizador',
            'email' => 'viewer@municipal.gov',
            'password' => Hash::make('viewer123'),
            'role' => 'viewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}