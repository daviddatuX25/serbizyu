<?php

namespace App\Domains\Users\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Common\Models\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;
use App\Domains\Common\Models\UserAddress;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;
use Plank\Mediable\Media;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\Common\Models\Image;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable implements MustVerifyEmail, MediableInterface
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Mediable, Searchable;

    protected $guard_name = 'web';

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

    /**
     * The addresses that belong to the user.
     */
    public function addresses()
    {
        return $this->belongsToMany(Address::class, 'user_addresses')
                    ->withPivot('is_primary')
                    ->withTimestamps();
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

    // Convenience method
    /**
     * Get the user's profile image (latest with tag 'profile_image')
     */

    public function getProfileImageAttribute()
    {
        return $this->media->where('tag', 'profile_image')->first();
    }
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function verification()
    {
        return $this->hasOne(UserVerification::class);
    }

    public function openOffers()
    {
        return $this->hasMany(\App\Domains\Listings\Models\OpenOffer::class, 'creator_id');
    }

    public function services()
    {
        return $this->hasMany(\App\Domains\Listings\Models\Service::class, 'creator_id');
    }

    public function bids()
    {
        return $this->hasMany(\App\Domains\Listings\Models\OpenOfferBid::class, 'bidder_id');
    }

    /**
     * Get the bookmarked workflow templates for the user.
     */
    public function bookmarkedWorkflows(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkflowTemplate::class,
            UserBookmarkedWorkflowTemplate::class,
            'user_id',
            'id',
            'id',
            'workflow_template_id'
        );
    }
    
    public function orders()
    {
        return $this->hasMany(\App\Domains\Orders\Models\Order::class, 'buyer_id');
    }
}
