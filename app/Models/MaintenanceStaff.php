<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceStaff extends Model
{
     use HasFactory;
    protected $table = 'Technicians'; 
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'specialization',
        'availability_status',
        'current_workload',
       
    ];

    /**
     * Get the user associated with the maintenance staff.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}