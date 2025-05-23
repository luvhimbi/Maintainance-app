<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
    'username',
    'password_hash',
    'email',
    'phone_number',
    'user_role',
    'status',
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
 public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    /**
     * Get the maintenance staff record associated with the user.
     */
    // app/Models/User.php
public function isAdmin()
{
    return $this->role === 'Admin';
}
public function mentions()
{
    return $this->hasMany(Mention::class);
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
}