<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'type',
        'status',
        'role_id',
        'avatar',
        'cover_image',
        'last_login',
        'create_time',
        'update_time',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            // Note: last_login cast removed as column may not exist in all database versions
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Get the name of the "remember me" token column.
     * Override to return null to disable remember token functionality
     * (the remember_token column doesn't exist in the database)
     *
     * @return string|null
     */
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Get the remember token value.
     * Override to return null since remember_token column doesn't exist
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the remember token value.
     * Override to do nothing since remember_token column doesn't exist
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Do nothing - column doesn't exist in database
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        $type = $this->type ?? $this->attributes['type'] ?? null;
        return $type === '1' || $type === 1;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        $status = $this->status ?? $this->attributes['status'] ?? null;
        return $status === '1' || $status === 1;
    }

    /**
     * Get booths owned by this user
     */
    public function booths()
    {
        return $this->hasMany(Booth::class, 'userid');
    }

    /**
     * Get bookings made by this user
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'userid');
    }

    /**
     * Get the role for this user
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the employee record for this user
     */
    public function employee()
    {
        return $this->hasOne(\App\Models\HR\Employee::class, 'user_id');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionSlug): bool
    {
        // Admin always has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Check if user's role has the permission
        if ($this->role) {
            return $this->role->hasPermission($permissionSlug);
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        foreach ($permissionSlugs as $slug) {
            if (!$this->hasPermission($slug)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for this user
     */
    public function getPermissions()
    {
        if ($this->isAdmin()) {
            return Permission::active()->get();
        }

        if ($this->role) {
            return $this->role->permissions()->active()->get();
        }

        return collect([]);
    }
}
