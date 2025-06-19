<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StaffMember extends Model
{    
    
    use HasFactory;
    // Table name in the database
    protected $table = 'staff_members';

    // Since user_id is the primary key
    protected $primaryKey = 'user_id';

    // Indicates the primary key is not auto-incrementing
    public $incrementing = false;

    // Primary key type is unsignedBigInteger
    protected $keyType = 'int';

    // Fields that are mass assignable
    protected $fillable = [
        'user_id',
        'department',
        'position_title',
    ];

    // Enable timestamps
    public $timestamps = true;

    /**
     * Get the user that this staff member belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
