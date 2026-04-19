<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@gmail.com',
            'password'  => Hash::make('Password1'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('  AdminSeeder: 1 admin dibuat.');
    }
}
