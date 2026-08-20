<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = ['nip', 'name', 'position', 'gender', 'phone', 'unique_code', 'rfid_code'];

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function todayAttendance()
    {
        return $this->hasOne(StaffAttendance::class)->whereDate('date', today());
    }
}
