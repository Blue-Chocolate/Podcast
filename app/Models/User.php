<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'age',
        'phone_number',
        'role', // 🔹 Add this if you’ll assign roles manually or via form
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    /**
     * Relationships
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'created_by');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Cached retrieval - returns collection of User models
     */
    public static function cachedAll()
    {
        return Cache::remember('users_all', now()->addMinutes(15), function () {
            return self::with('roles')->get();
        });
    }

    /**
     * Cached retrieval of a single user by ID - returns User model
     */
    public static function cachedFind($id)
    {
        return Cache::remember("user_{$id}", now()->addMinutes(15), function () use ($id) {
            return self::with('roles')->find($id);
        });
    }

    /**
     * Clear user cache
     */
    public static function clearCache($userId = null)
    {
        Cache::forget('users_all');
        if ($userId) {
            Cache::forget("user_{$userId}");
        }
    }

    /**
     * Model events for cache invalidation
     */
    protected static function booted()
    {
        static::saved(function ($user) {
            self::clearCache($user->id);
        });

        static::deleted(function ($user) {
            self::clearCache($user->id);
        });
    }
 
public function canAccessFilament(): bool
{
    return true;
}
// public function canAccessFilament(): bool
// {
//     return $this->role === 'admin';
// }
}