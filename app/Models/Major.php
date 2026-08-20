<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Major extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];
    public function classrooms(): HasMany { return $this->hasMany(Classroom::class); }
}
