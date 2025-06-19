<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'room_number'
    ];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function building()
    {
        return $this->hasOneThrough(Building::class, Floor::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }
} 