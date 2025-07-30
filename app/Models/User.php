<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'cover_image', // add this
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'password' => 'hashed',
        ];
    }

      public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }


    //for friendship 
    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    // For accepted friends (both sender or receiver accepted)
    // public function friends()
    // {
    //     return $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')
    //                 ->wherePivot('status', 'accepted')
    //                 ->withPivot('status')
    //                 ->withTimestamps();
    // }



    // Friendships where this user sent the request and accepted
public function sentFriends()
{
    return $this->belongsToMany(
        User::class,
        'friendships',
        'sender_id',
        'receiver_id'
    )->wherePivot('status', 'accepted');
}

// Friendships where this user received the request and accepted
public function receivedFriends()
{
    return $this->belongsToMany(
        User::class,
        'friendships',
        'receiver_id',
        'sender_id'
    )->wherePivot('status', 'accepted');
}

// Combine both sent and received accepted friends
public function friends()
{
    return $this->sentFriends->merge($this->receivedFriends);
}


}
