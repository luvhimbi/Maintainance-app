<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    use HasFactory;
    public $timestamps = false;
    // Define the table associated with the model
    protected $table = 'location';

    // Define the primary key
    protected $primaryKey = 'location_id';

    // Disable auto-incrementing if the primary key is not an integer
    public $incrementing = true;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'building_name',
        'floor_number',
        'room_number',
        'description',
    ];

    // Define relationship with the Issue model
    public function issues()
    {
        return $this->hasMany(Issue::class, 'location_id', 'location_id');
    }
}
