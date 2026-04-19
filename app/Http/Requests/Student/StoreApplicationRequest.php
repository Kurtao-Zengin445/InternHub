<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isStudent();
    }

    public function rules(): array
    {
        return [
            'internship_program_id' => 'required|exists:internship_programs,id',
            'motivation' => 'required|string|min:100|max:2000',
            'cv_file' => 'required|file|mimes:pdf|max:5120',
            'transcript_file' => 'sometimes|file|mimes:pdf|max:5120',
            'other_documents.*' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'motivation.min' => 'Motivasi minimal harus berisi 100 karakter',
            'cv_file.required' => 'Silakan unggah file CV Anda',
            'cv_file.mimes' => 'File CV harus berformat PDF',
            'cv_file.max' => 'Ukuran file CV maksimal 5MB',
        ];
    }
}
