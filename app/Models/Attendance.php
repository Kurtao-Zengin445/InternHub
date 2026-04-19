<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_PERMIT = 'permit';

    protected $fillable = [
        'internship_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'check_in_photo',
        'checkin_latitude',
        'checkin_longitude',
        'checkin_address',
        'checkin_distance',
        'check_out_photo',
        'checkout_latitude',
        'checkout_longitude',
        'checkout_address',
        'checkout_distance',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function getWorkHoursAttribute()
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return 0;
        }
        return round($this->check_in_time->diffInHours($this->check_out_time), 2);
    }

    public function isLate()
    {
        if (!$this->check_in_time) {
            return false;
        }
        return $this->check_in_time->format('H:i') > '08:00';
    }

    public function checkIn($latitude = null, $longitude = null)
    {
        $status = Carbon::now()->format('H:i') > '08:00' ? self::STATUS_LATE : self::STATUS_PRESENT;

        $this->update([
            'check_in_time' => now(),
            'status' => $status,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function checkOut()
    {
        $this->update([
            'check_out_time' => now(),
        ]);

        if ($this->internship) {
            $totalHours = $this->internship->attendances()->sum(DB::raw('TIMESTAMPDIFF(HOUR, check_in_time, check_out_time)'));
            $this->internship->update([
                'total_hours_completed' => $totalHours
            ]);
        }
    }

    public static function getTodayForInternship($internshipId)
    {
        return self::where('internship_id', $internshipId)
            ->whereDate('date', today())
            ->first();
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'permit' => 'Izin',
            default => ucfirst($this->status)
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'present' => 'bg-green-500',
            'absent' => 'bg-red-500',
            'late' => 'bg-yellow-500',
            'permit' => 'bg-blue-500',
            default => 'bg-gray-500'
        };
    }

    public function isCheckedIn()
    {
        return !is_null($this->check_in);
    }

    public function isCheckedOut()
    {
        return !is_null($this->check_out);
    }

    public function canCheckOut()
    {
        return $this->isCheckedIn() && !$this->isCheckedOut();
    }

    public function getCheckInTimeFormattedAttribute()
    {
        return $this->check_in ? \Carbon\Carbon::parse($this->check_in)->format('H:i') : '-';
    }

    public function getCheckOutTimeFormattedAttribute()
    {
        return $this->check_out ? \Carbon\Carbon::parse($this->check_out)->format('H:i') : '-';
    }

    public function getDurationMinutesAttribute()
    {
        if (!$this->check_in || !$this->check_out) return 0;
        return \Carbon\Carbon::parse($this->check_in)->diffInMinutes(\Carbon\Carbon::parse($this->check_out));
    }

    public function getDurationFormattedAttribute()
    {
        $minutes = $this->duration_minutes;
        if ($minutes == 0) return '-';
        return sprintf('%d jam %d menit', floor($minutes / 60), $minutes % 60);
    }

    public function hasLocation()
    {
        return !is_null($this->checkin_latitude) && !is_null($this->checkin_longitude);
    }

    public function duration()
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        $checkIn = \Carbon\Carbon::parse($this->check_in);
        $checkOut = \Carbon\Carbon::parse($this->check_out);
        $minutes = $checkIn->diffInMinutes($checkOut);

        if ($minutes == 0) return null;

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return $hours > 0
            ? "{$hours} jam {$mins} menit"
            : "{$mins} menit";
    }
}
