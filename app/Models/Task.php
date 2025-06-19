<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TaskUpdate;
use App\Models\Issue;
use App\Models\User;
use App\Models\Admin;
use App\Models\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    public $timestamps = true;
    // Specify the table name if it's different from the model name
    protected $table = 'task';

    // Specify the primary key if it's different from the default 'id'
    protected $primaryKey = 'task_id';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'issue_id',
        'assignee_id',
        'admin_id',
        'assignment_date',
        'expected_completion',
        'issue_status',
        'priority',
    ];

    // Specify the fields that should be cast to native types
    protected $casts = [
        'assignment_date' => 'datetime',
        'expected_completion' => 'datetime',
    ];

    // Define relationships
    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }

    public function updates()
    {
        return $this->hasMany(TaskUpdate::class, 'task_id');
    }

    public function getIssueIcon()
    {
        return match($this->issue->issue_type) {
            'Plumbing' => 'fa-faucet-drip',
            'Electrical' => 'fa-bolt-lightning',
            'Furniture' => 'fa-couch',
            'HVAC' => 'fa-fan',
            'Internet' => 'fa-network-wired',
            'Cleaning' => 'fa-broom',
            default => 'fa-circle-exclamation'
        };
    }
}
