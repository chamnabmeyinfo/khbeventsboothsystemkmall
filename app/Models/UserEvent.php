<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model for Event Management System
 * Uses 'users' table (plural) from imported database
 */
class UserEvent extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

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
        'firstname',
        'lastname',
        'username',
        'email',
        'dial_code',
        'country_name',
        'city',
        'state',
        'zip',
        'address',
        'country_code',
        'mobile',
        'password',
        'image',
        'status',
        'kyc_status',
        'kyc_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'kyc_status' => 'integer',
        ];
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->firstname.' '.$this->lastname);
    }
}
