<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Task;
use App\Models\User;

class TaskUpdate extends Model
{
    use HasFactory;
    protected $table = 'task_update';
    protected $primaryKey = 'update_id'; // Set the primary key
    public $timestamps = false; // Disable timestamps

    protected $fillable = [
        'task_id',
        'staff_id',
        'update_description',
        'status_change',
        'update_timestamp',
    ];

    // Relationship to the Task model
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    // Relationship to the User model (staff)
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
