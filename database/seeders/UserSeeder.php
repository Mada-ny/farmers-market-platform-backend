<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Konan Kouamé',
            'email' => 'admin@farmmarket.ci',
            'password' => Hash::make('Admin1234!'),
            'role' => Role::Admin,
        ]);

        User::create([
            'name' => 'Adjoua Bénédicte Yao',
            'email' => 'superviseur@farmmarket.ci',
            'password' => Hash::make('Super1234!'),
            'role' => Role::Supervisor,
        ]);

        User::create([
            'name' => 'Koffi Ange Assoumou',
            'email' => 'operateur@farmmarket.ci',
            'password' => Hash::make('Oper1234!'),
            'role' => Role::Operator,
        ]);
    }
}
