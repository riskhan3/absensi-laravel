<?php

namespace App\Http\Controllers\Teacher\Concerns;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

/**
 * Trait untuk me-resolve Teacher berdasarkan user yang sedang login.
 *
 * Digunakan oleh AttendanceSessionController dan ClassroomScanController
 * agar logika tidak terduplikasi (DRY principle).
 */
trait ResolvesTeacher
{
    /**
     * Dapatkan model Teacher yang berelasi dengan user yang login.
     *
     * Urutan resolusi:
     * 1. Jika user memiliki teacher_id langsung → ambil Teacher by ID
     * 2. Jika tidak → cari Teacher yang berelasi ke user ini via relasi has-one
     */
    protected function resolveTeacher(): ?Teacher
    {
        $user = Auth::user();

        if ($user->teacher_id) {
            return Teacher::find($user->teacher_id);
        }

        return Teacher::whereHas('user', fn ($q) => $q->where('id', $user->id))->first();
    }
}
