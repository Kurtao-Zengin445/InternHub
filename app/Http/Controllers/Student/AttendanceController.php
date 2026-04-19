<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Helpers\StudentInternshipHelper;
use App\Models\Attendance;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    use StudentInternshipHelper;

    const DEFAULT_RADIUS = 500; // meter

    public function index()
    {
        $internship = $this->getActiveInternship();

        if (!$internship) {
            return view('student.attendance.index', [
                'internship' => null,
                'attendances' => null,
                'summary' => null,
                'hasActiveInternship' => false,
            ]);
        }

        $query = Attendance::where('internship_id', $internship->id);

        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('attendance_date', 'like', "%$search%")
                  ->orWhere('notes', 'like', "%$search%")
                  ->orWhere('status', 'like', "%$search%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $attendances = $query->latest('attendance_date')
            ->paginate(20)
            ->appends(request()->query());

        $summary = [
            'present'    => Attendance::where('internship_id', $internship->id)->where('status', 'present')->count(),
            'absent'     => Attendance::where('internship_id', $internship->id)->where('status', 'absent')->count(),
            'sick'       => Attendance::where('internship_id', $internship->id)->where('status', 'sick')->count(),
            'permission' => Attendance::where('internship_id', $internship->id)->where('status', 'permission')->count(),
            'percentage' => $internship->attendancePercentage(),
        ];

        return view('student.attendance.index', compact('internship', 'attendances', 'summary'))
            ->with('hasActiveInternship', true);
    }

    public function export()
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return redirect()->route('student.attendance.index')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StudentAttendanceExport($internship->id),
            'Riwayat-Presensi-' . $internship->id . '-' . now()->format('d-m-Y') . '.xlsx'
        );
    }

    public function today()
    {
        $internship = $this->getActiveInternship();

        if (!$internship) {
            return view('student.attendance.today', [
                'internship' => null,
                'today' => null,
                'companyLat' => null,
                'companyLng' => null,
                'allowedRadius' => self::DEFAULT_RADIUS,
                'hasCompanyCoord' => false,
                'hasActiveInternship' => false,
            ]);
        }

        $today = Attendance::where('internship_id', $internship->id)
            ->whereDate('attendance_date', today())->first();

        $company = $internship->application->program->company;
        $companyLat = $company->latitude;
        $companyLng = $company->longitude;
        $allowedRadius = $company->allowed_radius ?? self::DEFAULT_RADIUS;
        $hasCompanyCoord = $companyLat && $companyLng;

        return view('student.attendance.today', compact(
            'internship', 'today',
            'companyLat', 'companyLng',
            'allowedRadius', 'hasCompanyCoord'
        ))->with('hasActiveInternship', true);
    }

    public function checkIn(Request $request)
    {
        $internship = $this->requireActiveInternshipForWrite();

        if (!$internship) {
            return redirect()->route('student.attendance.today')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        $existing = Attendance::where('internship_id', $internship->id)
            ->whereDate('attendance_date', today())->first();
        if ($existing) return back()->with('error', 'Anda sudah melakukan check-in hari ini.');

        if (today()->lt($internship->start_date) || today()->gt($internship->end_date)) {
            return back()->with('error', 'Presensi hanya dapat dilakukan dalam rentang tanggal magang.');
        }

        $request->validate([
            'selfie'    => ['required', 'string'],
            'latitude'  => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'address'   => ['nullable', 'string', 'max:255'],
            'notes'     => ['nullable', 'string', 'max:255'],
        ], [
            'selfie.required'    => 'Foto selfie wajib diambil!',
            'latitude.required'  => 'Lokasi tidak terdeteksi. Aktifkan GPS dan coba lagi.',
            'longitude.required' => 'Lokasi tidak terdeteksi. Aktifkan GPS dan coba lagi.',
        ]);

        // Skip selfie requirement if not provided (for testing without camera)
        $photoPath = null;
        $distance = null;
        
        if (!empty($request->selfie)) {
            $photoPath = $this->saveSelfie($request->selfie, "checkin/{$internship->id}");
        }

        // Calculate distance if company has coordinates
        $company = $internship->application->program->company;
        if ($company->latitude && $company->longitude) {
            $distance = Attendance::haversineDistance(
                $request->latitude, $request->longitude,
                $company->latitude, $company->longitude
            );
            $allowedRadius = $company->allowed_radius ?? self::DEFAULT_RADIUS;
            if ($distance > $allowedRadius) {
                return back()->with('error',
                    "Anda berada {$distance} meter dari lokasi perusahaan. Maksimal {$allowedRadius} meter.");
            }
        }

        Attendance::create([
            'internship_id'     => $internship->id,
            'attendance_date'   => today(),
            'check_in'          => now()->format('H:i:s'),
            'status'            => 'present',
            'check_in_photo'    => $photoPath,
            'checkin_latitude'  => $request->latitude,
            'checkin_longitude' => $request->longitude,
            'checkin_address'   => $request->address,
            'checkin_distance'  => $distance,
            'notes'             => $request->notes,
        ]);

        return redirect()->route('student.attendance.today')
            ->with('success', 'Check-in berhasil pukul ' . now()->format('H:i') . '.' .
                ($distance !== null ? " Jarak: {$distance} m dari kantor." : ''));
    }

    public function checkOut(Request $request)
    {
        $internship = $this->requireActiveInternshipForWrite();

        if (!$internship) {
            return redirect()->route('student.attendance.today')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        $attendance = Attendance::where('internship_id', $internship->id)
            ->whereDate('attendance_date', today())->first();

        if (!$attendance) {
            return back()->with('error', 'Belum ada check-in hari ini.');
        }
        if ($attendance->check_out) return back()->with('error', 'Anda sudah melakukan check-out hari ini.');

        $request->validate([
            'selfie'    => ['required', 'string'],
            'latitude'  => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'address'   => ['nullable', 'string', 'max:255'],
        ], [
            'selfie.required'    => 'Foto selfie wajib diambil!',
            'latitude.required'  => 'Lokasi tidak terdeteksi. Aktifkan GPS dan coba lagi.',
            'longitude.required' => 'Lokasi tidak terdeteksi. Aktifkan GPS dan coba lagi.',
        ]);

        $company  = $internship->application->program->company;
        $distance = null;
        if ($company->latitude && $company->longitude) {
            $distance = Attendance::haversineDistance(
                $request->latitude, $request->longitude,
                $company->latitude, $company->longitude
            );
            $allowedRadius = $company->allowed_radius ?? self::DEFAULT_RADIUS;
            if ($distance > $allowedRadius) {
                return back()->with('error',
                    "Anda berada {$distance} meter dari lokasi perusahaan. Maksimal {$allowedRadius} meter.");
            }
        }

        // Save selfie photo only if provided
        $photoPath = null;
        if (!empty($request->selfie)) {
            $photoPath = $this->saveSelfie($request->selfie, "checkout/{$internship->id}");
        }

        $attendance->update([
            'check_out'          => now()->format('H:i:s'),
            'check_out_photo'    => $photoPath,
            'checkout_latitude'  => $request->latitude,
            'checkout_longitude' => $request->longitude,
            'checkout_address'   => $request->address,
            'checkout_distance'  => $distance,
        ]);

        return redirect()->route('student.attendance.today')
            ->with('success', 'Check-out berhasil pukul ' . now()->format('H:i') . '. ' .
                ($attendance->duration() ? 'Total: ' . $attendance->duration() . '.' : ''));
    }

    public function storeLeave(Request $request)
    {
        $internship = $this->requireActiveInternshipForWrite();

        if (!$internship) {
            return redirect()->route('student.attendance.today')
                ->with('error', 'Anda belum memiliki magang aktif.');
        }

        $exists = Attendance::where('internship_id', $internship->id)
            ->whereDate('attendance_date', today())->exists();
        if ($exists) return back()->with('error', 'Presensi hari ini sudah tercatat.');

        $validated = $request->validate([
            'status' => ['required', 'in:sick,permission'],
            'notes'  => ['required', 'string', 'min:10', 'max:500'],
        ]);

        Attendance::create([
            'internship_id'   => $internship->id,
            'attendance_date' => today(),
            'status'          => $validated['status'],
            'notes'           => $validated['notes'],
        ]);

        $label = $validated['status'] === 'sick' ? 'Sakit' : 'Izin';
        return redirect()->route('student.attendance.today')
            ->with('success', "{$label} berhasil dicatat.");
    }

    public function show(Attendance $attendance)
    {
        $internship = $this->getActiveInternship();

        if (!$internship || $attendance->internship_id !== $internship->id) {
            abort(403);
        }

        return view('student.attendance.show', compact('attendance'));
    }

    private function saveSelfie(string $base64, string $folder): string
    {
        // Remove data URL prefix and get the actual base64 data
        $base64Data = $base64;
        
        // Handle different data URL formats
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            $base64Data = substr($base64, strpos($base64, ',') + 1);
        }
        
        // Clean up any whitespace or newlines
        $base64Data = trim($base64Data);
        
        // Decode the base64 data
        $imageData = base64_decode($base64Data, true);
        
        if ($imageData === false) {
            throw new \Exception('Failed to decode base64 image data');
        }
        
        // Verify it's a valid JPEG - must start with FF D8
        if (strlen($imageData) < 100 || substr($imageData, 0, 2) !== "\xFF\xD8") {
            throw new \Exception('Image data too small or invalid format - please try taking photo again with better lighting');
        }
        
        $filename = $folder . '/' . uniqid('selfie_', true) . '.jpg';
        Storage::disk('public')->put($filename, $imageData);
        return $filename;
    }
}