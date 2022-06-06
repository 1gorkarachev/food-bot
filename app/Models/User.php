<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'surname',
        'birth',
        'phone',
        'city',
        'photo',
        'email',
        'password',
        'birth_text',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getFullNameAttribute()
    {
        return "$this->name $this->surname";
    }

    public function getProfileDiskAttribute()
    {
        return Storage::disk('profile');
    }

    public function getProfilePhotoAttribute()
    {
        if ($this->photo) {
            return $this->profile_disk->exists($this->photo) ? $this->profile_disk->get($this->photo) : null;
        }

        return null;
    }
}
