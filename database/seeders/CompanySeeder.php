<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'user' => [
                    'name'  => 'TechCorp Indonesia',
                    'email' => 'techcorp@intern.test',
                ],
                'company' => [
                    'name'                 => 'PT TechCorp Indonesia',
                    'address'              => 'Gedung Sudirman Tower Lt.15, Jl. Jend. Sudirman Kav.25, Jakarta Selatan',
                    'phone'                => '021-52901234',
                    'email'                => 'hrd@techcorp.id',
                    'contact_person'       => 'Budi Santoso',
                    'contact_person_phone' => '081234567890',
                    'description'          => 'Perusahaan teknologi informasi terkemuka yang bergerak di bidang pengembangan perangkat lunak, cloud computing, dan solusi digital enterprise.',
                    'industry'             => 'Teknologi Informasi',
                    'website'              => 'https://techcorp.id',
                ],
            ],
            [
                'user' => [
                    'name'  => 'Maju Bersama Finance',
                    'email' => 'majubersama@intern.test',
                ],
                'company' => [
                    'name'                 => 'PT Maju Bersama Finance',
                    'address'              => 'Jl. Asia Afrika No.114, Bandung',
                    'phone'                => '022-4230456',
                    'email'                => 'hr@majubersama.co.id',
                    'contact_person'       => 'Dewi Kusumawati',
                    'contact_person_phone' => '082134567891',
                    'description'          => 'Perusahaan jasa keuangan yang menyediakan layanan pembiayaan, investasi, dan manajemen aset untuk segmen retail dan korporasi.',
                    'industry'             => 'Keuangan & Perbankan',
                    'website'              => 'https://majubersama.co.id',
                ],
            ],
            [
                'user' => [
                    'name'  => 'Nusantara Media Group',
                    'email' => 'nusantaramedia@intern.test',
                ],
                'company' => [
                    'name'                 => 'PT Nusantara Media Group',
                    'address'              => 'Jl. Pemuda No.55, Surabaya',
                    'phone'                => '031-5017890',
                    'email'                => 'intern@nusantaramedia.com',
                    'contact_person'       => 'Rizky Pratama',
                    'contact_person_phone' => '085312345678',
                    'description'          => 'Perusahaan media dan komunikasi yang bergerak di bidang penerbitan digital, produksi konten, dan periklanan.',
                    'industry'             => 'Media & Komunikasi',
                    'website'              => 'https://nusantaramedia.com',
                ],
            ],
        ];

        foreach ($companies as $data) {
            $user = User::create([
                'name'      => $data['user']['name'],
                'email'     => $data['user']['email'],
                'password'  => Hash::make('Password1'),
                'role'      => 'company',
                'is_active' => true,
            ]);

            Company::create(array_merge($data['company'], ['user_id' => $user->id]));
        }

        $this->command->info('  CompanySeeder: ' . count($companies) . ' perusahaan dibuat.');
    }
}
