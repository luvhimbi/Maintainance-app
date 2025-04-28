<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusMember extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'member_type',
        'student_staff_id',
        'faculty_department',
        'program_course',
        'year_of_study',
        'position_title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
