<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupervisorSeeder extends Seeder
{
    public function run(): void
    {
        $school1 = School::where('npsn', '20109859')->first(); // SMKN 1 Jakarta
        $school2 = School::where('npsn', '20532236')->first(); // SMKN 2 Surabaya
        $school3 = School::where('npsn', '20219463')->first(); // SMK Muhammadiyah Bandung

        $supervisors = [
            [
                'user' => [
                    'name'  => 'Agus Widodo, S.Kom.',
                    'email' => 'pembimbing@intern.test',
                ],
                'supervisor' => [
                    'school_id' => $school1->id,
                    'nip'       => '198501012010011001',
                    'position'  => 'Guru Produktif RPL',
                    'phone'     => '081298765432',
                ],
            ],
            [
                'user' => [
                    'name'  => 'Rina Marlina, S.T.',
                    'email' => 'rina.marlina@intern.test',
                ],
                'supervisor' => [
                    'school_id' => $school1->id,
                    'nip'       => '197803152005012002',
                    'position'  => 'Guru Teknik Komputer & Jaringan',
                    'phone'     => '082198765433',
                ],
            ],
            [
                'user' => [
                    'name'  => 'Bambang Haryanto, S.Pd.',
                    'email' => 'bambang.h@intern.test',
                ],
                'supervisor' => [
                    'school_id' => $school2->id,
                    'nip'       => '198709202012011003',
                    'position'  => 'Guru Akuntansi & Keuangan',
                    'phone'     => '083198765434',
                ],
            ],
            [
                'user' => [
                    'name'  => 'Lilis Suryani, M.Pd.',
                    'email' => 'lilis.s@intern.test',
                ],
                'supervisor' => [
                    'school_id' => $school3->id,
                    'nip'       => '198204102009012004',
                    'position'  => 'Guru Multimedia',
                    'phone'     => '084198765435',
                ],
            ],
        ];

        foreach ($supervisors as $data) {
            $user = User::create([
                'name'      => $data['user']['name'],
                'email'     => $data['user']['email'],
                'password'  => Hash::make('Password1'),
                'role'      => 'supervisor',
                'is_active' => true,
            ]);

            Supervisor::create(array_merge($data['supervisor'], ['user_id' => $user->id]));
        }

        $this->command->info('  SupervisorSeeder: ' . count($supervisors) . ' pembimbing dibuat.');
    }
}
