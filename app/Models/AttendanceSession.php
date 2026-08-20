<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = ['classroom_id', 'teacher_id', 'date', 'notes'];

    protected $casts = ['date' => 'date'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /** Mata pelajaran yang dicakup sesi ini */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'attendance_session_subjects');
    }

    /** Detail absen tiap siswa */
    public function studentAttendances(): HasMany
    {
        return $this->hasMany(SessionStudentAttendance::class);
    }

    /** Helper: label mapel gabung */
    public function getSubjectNamesAttribute(): string
    {
        return $this->subjects->pluck('name')->join(', ') ?: '—';
    }
}
