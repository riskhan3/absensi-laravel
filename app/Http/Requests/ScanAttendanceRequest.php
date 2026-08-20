<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanAttendanceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'scan_code'   => ['required', 'string', 'max:100'],
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', 'exists:subjects,id'],
            'teacher_id'  => ['nullable', 'integer', 'exists:teachers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'scan_code.required' => 'Kode scan tidak boleh kosong.',
            'latitude.required'  => 'Koordinat GPS (latitude) tidak terdeteksi.',
            'longitude.required' => 'Koordinat GPS (longitude) tidak terdeteksi.',
        ];
    }
}
