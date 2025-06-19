<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_name',
        'latitude',
        'longitude'
    ];

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    public function issues()
    {
        return $this->hasManyThrough(Issue::class, Floor::class);
    }
} 