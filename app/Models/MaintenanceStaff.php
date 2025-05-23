<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceStaff extends Model
{

    protected $table = 'maintenance_staff';
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