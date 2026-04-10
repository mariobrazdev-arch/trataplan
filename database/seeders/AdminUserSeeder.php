<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@trataplan.com.br'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Trataplan@2024!'),
                'is_admin' => true,
            ]
        );
    }
}
