<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Thread;
use App\Models\Reply;

class Category extends Model
{
    protected $fillable = ['name', 'user_id', 'is_custom'];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }
}