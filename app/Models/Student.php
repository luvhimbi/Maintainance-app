<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Student extends Model
{
    use HasFactory, Searchable;

    protected $table = 'students';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'student_number',
        'course',
        'faculty',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Make student details searchable with Scout.
     */
    public function toSearchableArray(): array
    {
        return [
            'student_number' => strtolower($this->student_number ?? ''),
            'course' => strtolower($this->course ?? ''),
            'faculty' => strtolower($this->faculty ?? ''),
            // Optionally include user details if loaded
            'first_name' => strtolower(optional($this->user)->first_name ?? ''),
            'last_name' => strtolower(optional($this->user)->last_name ?? ''),
            'email' => strtolower(optional($this->user)->email ?? ''),
        ];
    }
}
