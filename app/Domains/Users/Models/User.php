<?php

namespace App\Domains\Users\Models;

use App\Domains\Common\Models\Address;
use App\Domains\Common\Models\Image;
use App\Domains\Listings\Models\WorkflowTemplate;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Plank\Mediable\Mediable;
use Plank\Mediable\MediableInterface;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MediableInterface, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Mediable, Notifiable, Searchable;

    protected $guard_name = 'web';

    /**
     * The relations to always eager-load
     */
    protected $with = ['media'];

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

    /**
     * Get the user's profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        $profileImage = $this->getProfileImageAttribute();
        if ($profileImage) {
            return $profileImage->getUrl();
        }
        // Fallback to avatar with user's name
        $name = $this->name ?? 'User';

        return 'https://ui-avatars.com/api/?name='.urlencode($name);
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

    public function flagStats()
    {
        return $this->hasOne(CreatorFlagStats::class);
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

    /**
     * Get all reviews written by this user
     */
    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(UserReview::class, 'reviewer_id');
    }

    /**
     * Get all reviews received about this user
     */
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(UserReview::class, 'reviewee_id');
    }

    /**
     * Get average rating from received reviews
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviewsReceived()->avg('rating') ?? 0;
    }

    // static factory
    public static function factory()
    {
        return \Database\Factories\UserFactory::new();
    }
}
