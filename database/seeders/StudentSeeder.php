<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $school1 = School::where('npsn', '20109859')->first(); // SMKN 1 Jakarta
        $school2 = School::where('npsn', '20532236')->first(); // SMKN 2 Surabaya
        $school3 = School::where('npsn', '20219463')->first(); // SMK Muhammadiyah Bandung

        $students = [
            // SMKN 1 Jakarta — RPL
            [
                'user' => ['name' => 'Budi Prasetyo',    'email' => 'siswa@intern.test'],
                'student' => ['school_id' => $school1->id, 'nis' => '2122001', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak',    'gender' => 'male',   'birth_date' => '2006-03-15', 'birth_place' => 'Jakarta',  'phone' => '081311111111'],
            ],
            [
                'user' => ['name' => 'Sari Dewi Lestari', 'email' => 'sari.dewi@intern.test'],
                'student' => ['school_id' => $school1->id, 'nis' => '2122002', 'class' => 'XII RPL 1', 'major' => 'Rekayasa Perangkat Lunak',    'gender' => 'female', 'birth_date' => '2006-07-22', 'birth_place' => 'Bekasi',   'phone' => '081322222222'],
            ],
            [
                'user' => ['name' => 'Riko Firmansyah',   'email' => 'riko.f@intern.test'],
                'student' => ['school_id' => $school1->id, 'nis' => '2122003', 'class' => 'XII RPL 2', 'major' => 'Rekayasa Perangkat Lunak',    'gender' => 'male',   'birth_date' => '2006-11-08', 'birth_place' => 'Depok',    'phone' => '081333333333'],
            ],
            [
                'user' => ['name' => 'Nisa Auliandari',   'email' => 'nisa.a@intern.test'],
                'student' => ['school_id' => $school1->id, 'nis' => '2122004', 'class' => 'XII TKJ 1', 'major' => 'Teknik Komputer & Jaringan', 'gender' => 'female', 'birth_date' => '2006-05-19', 'birth_place' => 'Tangerang', 'phone' => '081344444444'],
            ],

            // SMKN 2 Surabaya — Akuntansi
            [
                'user' => ['name' => 'Dimas Arya Putra',  'email' => 'dimas.a@intern.test'],
                'student' => ['school_id' => $school2->id, 'nis' => '2122101', 'class' => 'XII AKL 1', 'major' => 'Akuntansi & Keuangan Lembaga', 'gender' => 'male',   'birth_date' => '2006-01-30', 'birth_place' => 'Surabaya',  'phone' => '081355555555'],
            ],
            [
                'user' => ['name' => 'Fitri Wahyuni',     'email' => 'fitri.w@intern.test'],
                'student' => ['school_id' => $school2->id, 'nis' => '2122102', 'class' => 'XII AKL 1', 'major' => 'Akuntansi & Keuangan Lembaga', 'gender' => 'female', 'birth_date' => '2006-09-14', 'birth_place' => 'Sidoarjo',  'phone' => '081366666666'],
            ],

            // SMK Muhammadiyah Bandung — Multimedia
            [
                'user' => ['name' => 'Galih Setiawan',    'email' => 'galih.s@intern.test'],
                'student' => ['school_id' => $school3->id, 'nis' => '2122201', 'class' => 'XII MM 1',  'major' => 'Multimedia',                  'gender' => 'male',   'birth_date' => '2006-06-25', 'birth_place' => 'Bandung',   'phone' => '081377777777'],
            ],
            [
                'user' => ['name' => 'Hana Pertiwi',      'email' => 'hana.p@intern.test'],
                'student' => ['school_id' => $school3->id, 'nis' => '2122202', 'class' => 'XII MM 1',  'major' => 'Multimedia',                  'gender' => 'female', 'birth_date' => '2006-04-11', 'birth_place' => 'Cimahi',    'phone' => '081388888888'],
            ],
        ];

        foreach ($students as $data) {
            $user = User::create([
                'name'      => $data['user']['name'],
                'email'     => $data['user']['email'],
                'password'  => Hash::make('Password1'),
                'role'      => 'student',
                'is_active' => true,
            ]);

            Student::create(array_merge($data['student'], ['user_id' => $user->id]));
        }

        $this->command->info('  StudentSeeder: ' . count($students) . ' siswa dibuat.');
    }
}
