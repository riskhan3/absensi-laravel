<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nuptk', 'nip', 'nik', 'name', 'gender', 'birth_date',
        'employment_status', 'position', 'address', 'phone',
        'unique_code', 'rfid_code', 'user_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo         { return $this->belongsTo(User::class); }
    public function homeroomClass(): HasOne    { return $this->hasOne(Classroom::class, 'homeroom_teacher_id'); }
    public function attendances(): HasMany     { return $this->hasMany(TeacherAttendance::class); }
    public function attendanceSessions(): HasMany { return $this->hasMany(AttendanceSession::class); }

    public function todayAttendance(): HasOne
    {
        return $this->hasOne(TeacherAttendance::class)->whereDate('date', today());
    }

    /** Mata pelajaran yang diajarkan guru ini */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects')
                    ->withPivot('classroom_id')->withTimestamps();
    }

    /** Apakah guru ini adalah wali kelas? */
    public function getIsHomeroomAttribute(): bool
    {
        return $this->homeroomClass()->exists();
    }
}
