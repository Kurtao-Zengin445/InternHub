<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Helpers\StudentInternshipHelper;
use App\Models\Document;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InternshipController extends Controller
{
    use StudentInternshipHelper;

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
        ];
    }

    public function show()
    {
        $student = auth()->guard()->user()->student;
        $internship = $student->activeInternship()
            ?? $student->internships()->with([
                'application.program.company',
                'supervisor.user',
            ])->latest()->first();

        return view('student.internship.show', compact('internship'));
    }

    public function documents()
    {
        $internship = $this->getActiveInternship();

        if (!$internship) {
            return view('student.internship.documents', [
                'internship' => null,
                'documents' => null,
                'hasActiveInternship' => false,
            ]);
        }

        $documents = Document::where('internship_id', $internship->id)
            ->latest('uploaded_at')
            ->get()
            ->groupBy('document_type');

        return view('student.internship.documents', compact('internship', 'documents'))
            ->with('hasActiveInternship', true);
    }

    public function uploadDocument(Request $request)
    {
        $internship = $this->requireActiveInternshipForWrite();

        if (!$internship) {
            return back()->with('error', 'Anda belum memiliki magang aktif.');
        }

        $validated = $request->validate([
            'document_type' => ['required', 'in:introduction_letter,acceptance_letter,activity_plan,progress_report,final_report,certificate,other'],
            'title'         => ['required', 'string', 'max:255'],
            'file'          => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $file     = $request->file('file');
        $filePath = $file->store("documents/{$internship->id}", 'public');

        $document = Document::create([
            'internship_id'  => $internship->id,
            'document_type'  => $validated['document_type'],
            'title'          => $validated['title'],
            'file_path'      => $filePath,
            'file_name'      => $file->getClientOriginalName(),
            'file_type'      => $file->getClientOriginalExtension(),
            'file_size'      => $file->getSize(),
            'uploaded_by'    => auth()->guard('web')->user()->id,
            'status'         => 'pending',
        ]);

        if ($internship->supervisor_id) {
            Notification::send(
                $internship->supervisor->user_id,
                'Dokumen Baru Diunggah',
                auth()->guard('web')->user()->name . " mengunggah dokumen: {$document->title}.",
                'info',
                $document,
                route('supervisor.documents.show', $document)
            );
        }

        return back()->with('success', 'Dokumen berhasil diunggah dan menunggu verifikasi.');
    }

    public function downloadDocument(Document $document)
    {
        $internship = $this->getActiveInternship();

        if (!$internship || $document->internship_id !== $internship->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download(Storage::disk('public')->path($document->file_path), $document->file_name);
    }

    public function evaluation()
    {
        $student = auth()->guard('web')->user()->student;
        $internship = $student->internships()
            ->with(['evaluations.evaluator', 'application.program.company', 'supervisor.user'])
            ->latest()
            ->first();

        if (!$internship) {
            return view('student.internship.evaluation', [
                'internship' => null,
                'supervisorEval' => null,
                'companyEval' => null,
                'finalScore' => null,
            ]);
        }

        $supervisorEval = $internship->supervisorEvaluation();
        $companyEval    = $internship->companyEvaluation();
        $finalScore     = $internship->finalScore();

        return view('student.internship.evaluation', compact(
            'internship',
            'supervisorEval',
            'companyEval',
            'finalScore',
        ));
    }
}