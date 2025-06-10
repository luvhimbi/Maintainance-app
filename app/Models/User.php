<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Searchable;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
  protected $fillable = [
    'user_id',
    'password_hash',
    'email',
    'phone_number',
    'user_role',
    'first_name',
    'last_name',
    'address'
];

    public function getAuthPassword()
    {
        return $this->password_hash; // Use the `password_hash` field
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash', // Hide password_hash instead of password
        'remember_token',
    ];

    public function hasRole(string $role): bool
    {
        return $this->user_role === $role;
    }


    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password_hash' => 'hashed', // Cast password_hash as hashed
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
public function studentDetail()
{
    return $this->hasOne(Student::class, 'user_id');
}
public function staffDetail()
{
    return $this->hasOne(StaffMember::class, 'user_id');
}
    public function feedbacks()
{
    return $this->hasMany(Feedback::class);
}
    /**
     * Get the admin record associated with the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }





public function isAdmin()
{
    return $this->role === 'Admin';
}

public function isMaintenanceStaff()
{
    return $this->role === 'Technician';
}

public function isStudent()
{
    return $this->role === 'Student';
}
public function maintenanceStaff()
{
        return $this->hasOne(MaintenanceStaff::class, 'user_id');
 }

 public function sendWelcomeNotification()
    {
        $this->notify(new DatabaseNotification('Welcome to our app support!', url('/')));
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

     public function campusMember(): HasOne
    {
        return $this->hasOne(CampusMember::class);
    }
    public function issues()
    {
        return $this->hasMany(Issue::class, 'reporter_id'); // Assuming 'user_id' is the foreign key in the 'issues' table
    }

    public function toSearchableArray(): array
    {
        return [
            'first_name' => strtolower($this->first_name ?? ''),
            'last_name' => strtolower($this->last_name ?? ''),
            'email' => strtolower($this->email ?? ''),
            'phone_number' => strtolower($this->phone_number ?? ''),
            'address' => strtolower($this->address ?? ''),
            // Add student number and course if relationship loaded
            'student_number' => strtolower(optional($this->studentDetail)->student_number ?? ''),
            'course' => strtolower(optional($this->studentDetail)->course ?? ''),
        ];
    }

}
