<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'action_url',
        'notifiable_type',
        'notifiable_id',
        'sent_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read'  => 'boolean',
            'sent_at'  => 'datetime',
            'read_at'  => 'datetime',
        ];
    }

    // ─── Relationships ─────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic — bisa ke internship, application, document, dll
    public function notifiable()
    {
        return $this->morphTo();
    }

    // ─── Helper ────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Kirim notifikasi ke satu user.
     * Contoh: Notification::send($userId, 'Laporan Disetujui', '...', 'approval', $internship);
     */
    public static function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?Model $notifiable = null,
        ?string $actionUrl = null
    ): self {
        return self::create([
            'user_id'          => $userId,
            'title'            => $title,
            'message'          => $message,
            'type'             => $type,
            'action_url'       => $actionUrl,
            'notifiable_type'  => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'    => $notifiable?->id,
        ]);
    }
}