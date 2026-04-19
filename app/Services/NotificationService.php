<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    public static function send(
        int|User $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?\Illuminate\Database\Eloquent\Model $notifiable = null,
        ?string $actionUrl = null
    ): Notification {
        $userId = $userId instanceof User ? $userId->id : $userId;
        
        return Notification::create([
            'user_id'          => $userId,
            'title'            => $title,
            'message'          => $message,
            'type'             => $type,
            'action_url'       => $actionUrl,
            'notifiable_type'  => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'    => $notifiable?->id,
            'sent_at'          => now(),
        ]);
    }

    public static function sendToMultiple(
        Collection|array $users,
        string $title,
        string $message,
        string $type = 'info',
        ?\Illuminate\Database\Eloquent\Model $notifiable = null,
        ?string $actionUrl = null
    ): void {
        $users = collect($users)->map(fn($u) => $u instanceof User ? $u->id : $u);
        
        $notifications = $users->map(fn($userId) => [
            'user_id'          => $userId,
            'title'            => $title,
            'message'          => $message,
            'type'             => $type,
            'action_url'       => $actionUrl,
            'notifiable_type'  => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'    => $notifiable?->id,
            'sent_at'          => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        Notification::insert($notifications->toArray());
    }

    public static function applicationStatusChanged(
        \App\Models\Application $application,
        string $status
    ): void {
        $student = $application->student;
        $user = $student->user;
        
        $statusLabels = [
            'pending' => 'Menunggu Tinjauan',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ];

        $title = 'Status Lamaran Diperbarui';
        $message = "Lamaran Anda untuk posisi {$application->program->title} telah {$statusLabels[$status]}";
        $type = $status === 'accepted' ? 'success' : ($status === 'rejected' ? 'danger' : 'info');

        self::send($user->id, $title, $message, $type, $application, route('student.applications.show', $application));
    }

    public static function dailyReportStatusChanged(
        \App\Models\DailyReport $report,
        string $status
    ): void {
        $internship = $report->internship;
        $student = $internship->student;
        $user = $student->user;
        
        $statusLabels = [
            'approved' => 'Disetujui',
            'revision' => 'Memerlukan Revisi',
            'submitted' => 'Dikirim',
        ];

        $title = 'Status Laporan Harian Diperbarui';
        $message = "Laporan harian tanggal {$report->report_date->format('d/m/Y')} telah {$statusLabels[$status]}";
        $type = $status === 'approved' ? 'success' : ($status === 'revision' ? 'warning' : 'info');

        self::send($user->id, $title, $message, $type, $report, route('student.reports.show', $report));
    }

    public static function internshipStatusChanged(
        \App\Models\Internship $internship,
        string $status
    ): void {
        $student = $internship->student;
        $user = $student->user;
        
        $statusLabels = [
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'terminated' => 'Dihentikan',
        ];

        $title = 'Status Magang Diperbarui';
        $message = "Status magang Anda telah diubah menjadi {$statusLabels[$status]}";
        $type = $status === 'completed' ? 'success' : ($status === 'terminated' ? 'danger' : 'info');

        self::send($user->id, $title, $message, $type, $internship, route('student.internship.show'));
    }

    public static function documentStatusChanged(
        \App\Models\Document $document,
        string $status
    ): void {
        $internship = $document->internship;
        $student = $internship->student;
        $user = $student->user;
        
        $statusLabels = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'pending' => 'Menunggu Tinjauan',
        ];

        $title = 'Status Dokumen Diperbarui';
        $message = "Dokumen {$document->title} telah {$statusLabels[$status]}";
        $type = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'info');

        self::send($user->id, $title, $message, $type, $document, route('student.internship.documents'));
    }
}