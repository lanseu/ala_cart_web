<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Lunar\Base\Traits\LunarUser;
use Lunar\Models\Customer;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, LunarUser, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $appends = ['full_name', 'profile_picture_url'];

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'address',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Full name accessor
    public function getFullNameAttribute()
    {
        return trim($this->first_name.' '.($this->middle_name ?? '').' '.$this->last_name);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function getProfilePictureUrlAttribute()
    {
        if (! $this->profile_picture) {
            return asset('storage/profile_pictures/default_profile.jpg'); // Default image
        }

        // Check if profile_picture is already a full URL (in case of external storage)
        if (filter_var($this->profile_picture, FILTER_VALIDATE_URL)) {
            return $this->profile_picture;
        }

        // Generate a full URL for local storage
        return asset('storage/'.$this->profile_picture);
    }
}
