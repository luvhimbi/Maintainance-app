<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Feedback extends Model
{
    use HasFactory;

    protected $fillable = ['issue_id', 'user_id', 'rating', 'comments'];

// In your Feedback model
public function issue()
{
    return $this->belongsTo(Issue::class, 'issue_id');
}
  public function user()
{
    return $this->belongsTo(User::class, 'user_id'); // Explicitly specify the foreign key
}
}
