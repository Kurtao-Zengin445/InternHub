<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            [
                'user' => [
                    'name'  => 'SMKN 1 Jakarta',
                    'email' => 'smkn1@intern.test',
                ],
                'school' => [
                    'name'           => 'SMK Negeri 1 Jakarta',
                    'address'        => 'Jl. Budi Utomo No.7, Gambir, Jakarta Pusat',
                    'phone'          => '021-3845041',
                    'email'          => 'smkn1jakarta@kemdikbud.go.id',
                    'principal_name' => 'Drs. Hendra Kusuma, M.Pd.',
                    'npsn'           => '20109859',
                    'school_type'    => 'SMK',
                ],
            ],
            [
                'user' => [
                    'name'  => 'SMKN 2 Surabaya',
                    'email' => 'smkn2sby@intern.test',
                ],
                'school' => [
                    'name'           => 'SMK Negeri 2 Surabaya',
                    'address'        => 'Jl. AM. Sangaji No.40, Krembangan, Surabaya',
                    'phone'          => '031-3523920',
                    'email'          => 'smkn2surabaya@kemdikbud.go.id',
                    'principal_name' => 'Dr. Siti Rahayu, M.M.',
                    'npsn'           => '20532236',
                    'school_type'    => 'SMK',
                ],
            ],
            [
                'user' => [
                    'name'  => 'SMK Muhammadiyah Bandung',
                    'email' => 'smkmuh@intern.test',
                ],
                'school' => [
                    'name'           => 'SMK Muhammadiyah 1 Bandung',
                    'address'        => 'Jl. Sancang No.6, Burangrang, Lengkong, Bandung',
                    'phone'          => '022-7305269',
                    'email'          => 'smkmuh1bandung@gmail.com',
                    'principal_name' => 'H. Ahmad Fauzi, S.Pd., M.T.',
                    'npsn'           => '20219463',
                    'school_type'    => 'SMK',
                ],
            ],
        ];

        foreach ($schools as $data) {
            $user = User::create([
                'name'      => $data['user']['name'],
                'email'     => $data['user']['email'],
                'password'  => Hash::make('Password1'),
                'role'      => 'school',
                'is_active' => true,
            ]);

            School::create(array_merge($data['school'], ['user_id' => $user->id]));
        }

        $this->command->info('  SchoolSeeder: ' . count($schools) . ' sekolah dibuat.');
    }
}
