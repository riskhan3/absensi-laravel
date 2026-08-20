<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'nis'          => ['required', 'string', 'max:16', Rule::unique('students', 'nis')->ignore($studentId)],
            'name'         => ['required', 'string', 'max:255'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'gender'       => ['required', 'in:Laki-laki,Perempuan'],
            'phone'        => ['nullable', 'string', 'max:32'],
            'rfid_code'    => ['nullable', 'string', 'max:100', Rule::unique('students', 'rfid_code')->ignore($studentId)],
        ];
    }
}
