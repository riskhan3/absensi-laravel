<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nis', 'nisn', 'name', 'classroom_id', 'gender',
        'birth_date', 'mother_name', 'nik',
        'phone', 'unique_code', 'rfid_code',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function classroom(): BelongsTo  { return $this->belongsTo(Classroom::class); }
    public function attendances(): HasMany  { return $this->hasMany(StudentAttendance::class); }

    public function todayAttendance(): HasOne
    {
        return $this->hasOne(StudentAttendance::class)->whereDate('date', today());
    }
}
