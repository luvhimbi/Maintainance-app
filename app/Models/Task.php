<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TaskUpdate;
class Task extends Model
{
    use HasFactory;
    public $timestamps = false;
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
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    public function updates()
    {
        return $this->hasMany(TaskUpdate::class, 'task_id');
    }
}
