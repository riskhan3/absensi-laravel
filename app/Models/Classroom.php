<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes;

    protected $fillable = ['grade', 'major_id', 'label', 'homeroom_teacher_id'];

    public function major(): BelongsTo         { return $this->belongsTo(Major::class); }
    public function homeroomTeacher(): BelongsTo { return $this->belongsTo(Teacher::class, 'homeroom_teacher_id'); }
    public function students(): HasMany         { return $this->hasMany(Student::class); }
    public function studentAttendances(): HasMany { return $this->hasMany(StudentAttendance::class); }

    /** Nama kelas lengkap, contoh: "Kelas IV A" */
    public function getFullNameAttribute(): string
    {
        return "Kelas {$this->grade} {$this->label}";
    }

    /** Konversi angka Romawi ke Arab: I→1, II→2, dst */
    public function getGradeNumberAttribute(): string
    {
        $map = ['I'=>'1','II'=>'2','III'=>'3','IV'=>'4','V'=>'5','VI'=>'6'];
        return $map[strtoupper($this->grade)] ?? $this->grade;
    }

    /** Label pendek: "Kelas 1" (tanpa huruf A/B) */
    public function getGradeLabelAttribute(): string
    {
        return 'Kelas ' . $this->grade_number;
    }
}
