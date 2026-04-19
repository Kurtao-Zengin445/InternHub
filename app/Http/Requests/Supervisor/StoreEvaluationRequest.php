<?php

namespace App\Http\Requests\Supervisor;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isSupervisor() || auth()->user()->isCompany();
    }

    public function rules(): array
    {
        return [
            'attitude_score' => 'required|integer|min:0|max:100',
            'knowledge_score' => 'required|integer|min:0|max:100',
            'skill_score' => 'required|integer|min:0|max:100',
            'discipline_score' => 'required|integer|min:0|max:100',
            'communication_score' => 'required|integer|min:0|max:100',
            'notes' => 'required|string|min:50|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'attitude_score.required' => 'Nilai sikap harus diisi',
            'knowledge_score.required' => 'Nilai pengetahuan harus diisi',
            'skill_score.required' => 'Nilai keterampilan harus diisi',
            'discipline_score.required' => 'Nilai kedisiplinan harus diisi',
            'communication_score.required' => 'Nilai komunikasi harus diisi',
            'notes.min' => 'Catatan evaluasi minimal 50 karakter',
        ];
    }
}
