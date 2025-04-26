<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relation;
use App\Models\Reply;
use App\Models\User;
use App\Models\Thread;

// app/Models/Mention.php
class Mention extends Model
{
    protected $fillable = ['user_id', 'thread_id', 'reply_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }
}


