<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    protected $fillable = ['teacher_id', 'date', 'time_in', 'time_out', 'status_id', 'notes', 'latitude', 'longitude'];

    protected $casts = ['date' => 'date'];

    public function teacher(): BelongsTo { return $this->belongsTo(Teacher::class); }
    public function status(): BelongsTo  { return $this->belongsTo(AttendanceStatus::class, 'status_id'); }
}
