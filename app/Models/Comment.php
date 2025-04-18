<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Issue;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'user_id',
        'comment',
    ];

    // Relationships
    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
