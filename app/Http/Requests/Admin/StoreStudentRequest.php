<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nis'          => ['required', 'string', 'max:16', 'unique:students,nis'],
            'name'         => ['required', 'string', 'max:255'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'gender'       => ['required', 'in:Laki-laki,Perempuan'],
            'phone'        => ['nullable', 'string', 'max:32'],
            'rfid_code'    => ['nullable', 'string', 'max:100', 'unique:students,rfid_code'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.unique'        => 'NIS sudah terdaftar.',
            'rfid_code.unique'  => 'Kode RFID sudah digunakan siswa lain.',
            'classroom_id.exists' => 'Kelas tidak ditemukan.',
        ];
    }
}
