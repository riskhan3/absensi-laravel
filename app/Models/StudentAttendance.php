<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentAttendance extends Model
{
    protected $fillable = [
        'student_id', 'classroom_id', 'teacher_id',
        'date', 'time_in', 'time_out', 'status_id',
        'notes', 'latitude', 'longitude',
    ];

    protected $casts = ['date' => 'date'];

    public function student(): BelongsTo   { return $this->belongsTo(Student::class); }
    public function classroom(): BelongsTo { return $this->belongsTo(Classroom::class); }
    public function teacher(): BelongsTo   { return $this->belongsTo(Teacher::class); }
    public function status(): BelongsTo    { return $this->belongsTo(AttendanceStatus::class, 'status_id'); }

    /** Mata pelajaran yang dicatat saat absen (multi-mapel) */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_attendance_subjects')->withTimestamps();
    }
}
