<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $fillable = ['staff_id', 'date', 'check_in', 'check_out', 'latitude', 'longitude', 'notes'];

    protected $casts = ['date' => 'date'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
