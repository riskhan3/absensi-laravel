<?php

namespace App\Http\Controllers\Teacher\Concerns;

use App\Models\AttendanceSession;
use App\Models\StudentAttendance;

/**
 * Menyinkronkan data session_student_attendances ke student_attendances
 * agar laporan admin bisa membaca absensi dari sesi guru.
 */
trait SyncsToStudentAttendance
{
    /**
     * Mirror semua record dari sebuah AttendanceSession ke tabel student_attendances.
     * Dipanggil setelah store/update sesi atau finalize scan.
     */
    protected function syncSessionToStudentAttendances(AttendanceSession $session): void
    {
        if (!$session->relationLoaded('studentAttendances')) {
            $session->load('studentAttendances');
        }

        foreach ($session->studentAttendances as $ssa) {
            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $ssa->student_id,
                    'date'       => $session->date->toDateString(),
                ],
                [
                    'classroom_id' => $session->classroom_id,
                    'teacher_id'   => $session->teacher_id,
                    'status_id'    => $ssa->attendance_status_id,
                    'notes'        => $ssa->notes,
                ]
            );
        }
    }

    /**
     * Mirror satu record (satu siswa) ke student_attendances.
     * Dipakai di ClassroomScanController saat scan individual.
     */
    protected function syncSingleAttendance(
        AttendanceSession $session,
        int $studentId,
        int $statusId,
        ?string $notes = null
    ): void {
        StudentAttendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'date'       => $session->date->toDateString(),
            ],
            [
                'classroom_id' => $session->classroom_id,
                'teacher_id'   => $session->teacher_id,
                'status_id'    => $statusId,
                'notes'        => $notes,
            ]
        );
    }
}
