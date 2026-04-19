<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Internship;
use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;

class InternshipSeeder extends Seeder
{
    public function run(): void
    {
        // Pembimbing sekolah
        $agus    = Supervisor::whereHas('user', fn($q) => $q->where('email', 'pembimbing@intern.test'))->first();
        $rina    = Supervisor::whereHas('user', fn($q) => $q->where('email', 'rina.marlina@intern.test'))->first();
        $bambang = Supervisor::whereHas('user', fn($q) => $q->where('email', 'bambang.h@intern.test'))->first();

        // Pembimbing dari perusahaan (user dengan role company)
        $techcorpUser  = User::where('email', 'techcorp@intern.test')->first();
        $financeUser   = User::where('email', 'majubersama@intern.test')->first();

        // Ambil lamaran yang accepted
        $acceptedApps = Application::where('status', 'accepted')
            ->with('program.company', 'studentModel', 'studentModel.school')
            ->get();

        foreach ($acceptedApps as $application) {
            $companyEmail = $application->program->company->user->email;

            // Tentukan pembimbing sekolah berdasarkan sekolah siswa
            $student = $application->studentModel;
            $schoolNpsn = $student?->school?->npsn;
            
            $supervisor = match ($schoolNpsn) {
                '20109859' => $application->program->field === 'Teknik Komputer & Jaringan' ? $rina : $agus,
                '20532236' => $bambang,
                default    => $agus,
            };

            // Pembimbing perusahaan
            $companySupervisorId = match ($companyEmail) {
                'techcorp@intern.test'       => $techcorpUser->id,
                'majubersama@intern.test'    => $financeUser->id,
                default                      => null,
            };

            Internship::create([
                'application_id'        => $application->id,
                'supervisor_id'         => $supervisor->id,
                'company_supervisor_id' => $companySupervisorId,
                'start_date'            => $application->program->start_date,
                'end_date'              => $application->program->end_date,
                'status'                => 'completed',
            ]);
        }

        $count = $acceptedApps->count();
        $this->command->info("  InternshipSeeder: {$count} data magang dibuat.");
    }
}
