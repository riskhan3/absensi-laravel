<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'class_group', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Guru yang mengajar mapel ini */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects')
                    ->withPivot('classroom_id')->withTimestamps();
    }

    /** Sesi absen yang memakai mapel ini */
    public function attendanceSessions(): BelongsToMany
    {
        return $this->belongsToMany(AttendanceSession::class, 'attendance_session_subjects');
    }

    /** Label class_group yang mudah dibaca */
    public function getClassGroupLabelAttribute(): string
    {
        return match($this->class_group) {
            '1-2'  => 'Kelas 1–2',
            '3-6'  => 'Kelas 3–6',
            default => 'Semua Kelas',
        };
    }
}
