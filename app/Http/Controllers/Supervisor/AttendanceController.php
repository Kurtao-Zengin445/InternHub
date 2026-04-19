<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Internship;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:supervisor',
        ];
    }

    public function index()
    {
        $supervisor = auth()->user()->supervisor;

        $attendances = Attendance::whereHas('internship', function ($q) use ($supervisor) {
                $q->where('supervisor_id', $supervisor->id);
            })
            ->with(['internship.student.user', 'internship.application.program.company'])
            ->latest('attendance_date')
            ->paginate(20);

        return view('supervisor.attendance.index', compact('attendances'));
    }

    public function show(Attendance $attendance)
    {
        $supervisor = auth()->user()->supervisor;

        abort_if($attendance->internship->supervisor_id !== $supervisor->id, 403);

        $attendance->load(['internship.student.user', 'internship.application.program']);

        return view('supervisor.attendance.show', compact('attendance'));
    }
}