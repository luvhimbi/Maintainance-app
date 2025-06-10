<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Location extends Model
{
    use HasFactory;
    use Searchable;
    public $timestamps = true;
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
        'latitude',
        'longitude'
    ];

    /**
     * Get the indexable data array for the model.
     * Make all searchable fields lowercase for case-insensitive search.
     */
    public function toSearchableArray(): array
    {
        return [
            'building_name' => strtolower($this->building_name ?? ''),
            'floor_number' => strtolower($this->floor_number ?? ''),
            'room_number' => strtolower($this->room_number ?? ''),
            'description' => strtolower($this->description ?? ''),
        ];
    }

    // Define relationship with the Issue model
    public function issues()
    {
        return $this->hasMany(Issue::class, 'location_id', 'location_id');
    }
}
