<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    public $timestamps = true;
    use HasFactory;
    // Define the table associated with the model
    protected $table = 'issue';

    // Define the primary key
    protected $primaryKey = 'issue_id';

    // Disable auto-incrementing if the primary key is not an integer
    public $incrementing = true;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'reporter_id',
        'location_id',
        'issue_type',
        'issue_description',
        'report_date',
        'issue_status',
        'urgency_level',
    ];

    // Define relationship with the Location model
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    // Define the comments relationship
    public function comments()
    {
        return $this->hasMany(Comment::class, 'issue_id');
    }
    // app/Models/Issue.php

public function feedback()
{
    return $this->hasMany(Feedback::class);
}

public function hasFeedbackFrom(User $user)
{
  return Feedback::where('issue_id', $this->issue_id)
        ->where('user_id', $user->user_id)
        ->exists();
}
    public function task()
{
    return $this->hasOne(Task::class, 'issue_id');
} public function reporter()
{
    return $this->belongsTo(User::class, 'reporter_id', 'user_id'); // assuming 'reported_by' is the foreign key

}


public function history()
{
    return $this->hasMany(HistoryLog::class)->latest();
}

public function tasks()
    {
        return $this->hasMany(Task::class, 'issue_id');
    }
    // Define relationship with the IssueAttachment model
    public function attachments()
    {
        return $this->hasMany(IssueAttachment::class, 'issue_id', 'issue_id');
    }

}
