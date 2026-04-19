<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReportNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $reportId,
        public string $action // 'approved' or 'rejected'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $report = \App\Models\DailyReport::with(['internship.application.student.user', 'internship.supervisor.user'])
            ->findOrFail($this->reportId);

        $student = $report->internship->application->student->user;
        $supervisor = $report->internship->supervisor->user;

        if ($this->action === 'approved') {
            \App\Mail\ReportApprovedMailable::to($student->email)
                ->queue(new \App\Mail\ReportApprovedMailable(
                    $student->name,
                    $supervisor->name,
                    $report->report_date->format('Y-m-d'),
                    $report->report_date->translatedFormat('d F Y'),
                    Str::limit($report->activity, 100),
                    $report->feedback,
                    url('/student/internship')
                ));
        } else {
            \App\Mail\ReportRejectedMailable::to($student->email)
                ->queue(new \App\Mail\ReportRejectedMailable(
                    $student->name,
                    $supervisor->name,
                    $report->report_date->format('Y-m-d'),
                    $report->report_date->translatedFormat('d F Y'),
                    $report->feedback,
                    url('/student/reports/' . $report->id . '/edit')
                ));
        }
    }
}
