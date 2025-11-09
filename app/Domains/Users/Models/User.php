<?php

namespace App\Domains\Users\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Common\Models\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Domains\Common\Models\UserAddress;

use App\Domains\Common\Models\Image;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web'; 
    protected $softDeletes = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'phone',
        'address_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // address
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    // Profile Image
    public function profileImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('collection_name', 'profile');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

}
