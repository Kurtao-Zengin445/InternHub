<?php

namespace Database\Seeders;

use App\Models\{
    Application, Attendance, Company, DailyReport,
    Document, Internship, InternshipProgram,
    School, Student, Supervisor, User
};
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 🔹 DATA USER (TAMBAH DI SINI)
            $users = [
                [
                    'name' => 'Budi Prasetyo',
                    'email' => 'siswa@intern.test',
                    'password' => 'Password1',
                ],
                [
                    'name' => 'Muhamad Naufal Al Biruni',
                    'email' => 'metalion1234@gmail.com',
                    'password' => 'Password1',
                ],
            ];

            // 🔹 Dependencies
            $school = School::first();
            $company = Company::first();
            $supervisor = Supervisor::first();

            if (!$school || !$company || !$supervisor) {
                $this->command->warn('Run base seeder first!');
                return;
            }

            // 🔹 PROGRAM (shared untuk semua user)
            $program = InternshipProgram::firstOrCreate([
                'company_id' => $company->id
            ], [
                'title' => 'Program Magang Aktif',
                'description' => 'Seeder Multi Student',
                'requirements' => 'Basic',
                'quota' => 20,
                'field' => 'IT',
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(60),
                'registration_start' => now()->subDays(45),
                'registration_end' => now()->subDays(15),
                'status' => 'active',
            ]);

            foreach ($users as $u) {

                // 🔹 USER
                $user = User::firstOrCreate(
                    ['email' => $u['email']],
                    [
                        'name' => $u['name'],
                        'password' => Hash::make($u['password']),
                    ]
                );

                // 🔹 STUDENT
                $student = Student::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'school_id' => $school->id,
                        'nis' => rand(100000, 999999),
                    ]
                );

                // 🔹 APPLICATION
                $application = Application::updateOrCreate(
                    [
                        'student_id' => $user->id,
                        'internship_program_id' => $program->id,
                    ],
                    [
                        'school_id' => $school->id,
                        'status' => 'accepted',
                        'applied_at' => now()->subDays(40),
                        'reviewed_at' => now()->subDays(35),
                    ]
                );

                // 🔹 INTERNSHIP
                $internship = Internship::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'supervisor_id' => $supervisor->id,
                        'company_supervisor_id' => $company->user_id,
                        'start_date' => now()->subDays(30),
                        'end_date' => now()->addDays(60),
                        'status' => 'active',
                    ]
                );

                // 🔹 DAILY REPORT
                DailyReport::where('internship_id', $internship->id)->delete();

                foreach (Carbon::now()->subDays(3)->daysUntil(now()) as $date) {
                    if ($date->isToday()) continue;

                    DailyReport::create([
                        'internship_id' => $internship->id,
                        'report_date' => $date->format('Y-m-d'),
                        'activity' => 'Mengerjakan task Laravel',
                        'status' => 'approved',
                    ]);
                }

                // 🔹 DOCUMENT
                Document::where('internship_id', $internship->id)->delete();

                Document::create([
                    'internship_id' => $internship->id,
                    'title' => 'Dokumen Test',
                    'document_type' => 'other',
                    'file_path' => 'test.pdf',
                    'file_name' => 'test.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 123456,
                    'uploaded_by' => $user->id,
                    'status' => 'pending',
                ]);

                $this->command->info("✔ User created: {$u['email']}");
            }

            $this->command->info("🚀 Semua user berhasil dibuat dengan magang aktif!");
        });
    }
}