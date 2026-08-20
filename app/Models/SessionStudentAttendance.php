<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionStudentAttendance extends Model
{
    protected $fillable = ['attendance_session_id', 'student_id', 'attendance_status_id', 'notes'];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function status()
    {
        return $this->belongsTo(AttendanceStatus::class, 'attendance_status_id');
    }
}
