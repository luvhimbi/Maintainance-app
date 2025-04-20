<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    //history logs
     use HasFactory;

    protected $table = 'issue_history_logs';

    protected $fillable = [
        'issue_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
