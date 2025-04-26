<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Reply extends Model
{
    use SoftDeletes;

    protected $fillable = ['content', 'user_id', 'thread_id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => '[Deleted User]'
        ]);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}