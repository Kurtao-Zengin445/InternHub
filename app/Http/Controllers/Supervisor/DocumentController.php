<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Internship;
use App\Models\Notification;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Daftar semua dokumen dari siswa bimbingan.
     */
    public function index(Request $request)
    {
        $supervisor    = $this->currentSupervisor();
        $internshipIds = Internship::where('supervisor_id', $supervisor->id)->pluck('id');

        $documents = Document::with(['internship.application.student.user', 'uploader'])
            ->whereIn('internship_id', $internshipIds)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('type'), fn($q) => $q->where('document_type', $request->type))
            ->latest('uploaded_at')
            ->paginate(15)
            ->withQueryString();

        return view('supervisor.documents.index', compact('documents'));
    }

    /**
     * Detail dokumen.
     */
    public function show(Document $document)
    {
        $this->authorizeDocument($document);

        $document->load('internship.application.student.user', 'uploader');

        return view('supervisor.documents.show', compact('document'));
    }

    /**
     * Download dokumen.
     */
    public function download(Document $document)
    {
        $this->authorizeDocument($document);

        abort_if(!Storage::disk('public')->exists($document->file_path), 404, 'File tidak ditemukan.');

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Setujui dokumen.
     */
    public function approve(Document $document)
    {
        $this->authorizeDocument($document);

        abort_if($document->status !== 'pending', 422, 'Dokumen tidak dalam status menunggu persetujuan.');

        $document->update(['status' => 'approved']);

        $student = $document->internship->application->student;
        Notification::send(
            $student->user_id,
            'Dokumen Disetujui',
            "Dokumen \"{$document->title}\" telah disetujui oleh pembimbing.",
            'approval',
            $document,
            route('student.internship.documents')
        );

        return back()->with('success', 'Dokumen berhasil disetujui.');
    }

    /**
     * Tolak dokumen dengan alasan.
     */
    public function reject(Request $request, Document $document)
    {
        $this->authorizeDocument($document);

        abort_if($document->status !== 'pending', 422, 'Dokumen tidak dalam status menunggu persetujuan.');

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $document->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $student = $document->internship->application->student;
        Notification::send(
            $student->user_id,
            'Dokumen Ditolak',
            "Dokumen \"{$document->title}\" ditolak. Silakan unggah ulang dengan perbaikan.",
            'warning',
            $document,
            route('student.internship.documents')
        );

        return back()->with('success', 'Dokumen berhasil ditolak.');
    }

    // ─── Private helper ────────────────────────────────────

    private function authorizeDocument(Document $document): void
    {
        $supervisor    = $this->currentSupervisor();
        $internshipIds = Internship::where('supervisor_id', $supervisor->id)->pluck('id');

        abort_if(!$internshipIds->contains($document->internship_id), 403);
    }

    private function currentSupervisor(): Supervisor
    {
        $supervisor = auth()->user()?->supervisor;

        abort_if(!$supervisor, 403, 'Profil pembimbing belum tersedia. Silakan hubungi admin.');

        return $supervisor;
    }
}
