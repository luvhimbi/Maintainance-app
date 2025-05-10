<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'user_id';
    public $timestamps =false;
    protected $fillable = ['user_id', 'department'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}