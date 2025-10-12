<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable,HasRoles;

    protected $fillable = ['name','email','password','role','avatar_url'];

    protected $hidden = ['password'];

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
 public function getAuthPassword()
{
    return $this->password;
}
protected static function booted()
{
    static::saved(function ($user) {
        if ($user->role) {
            // Make sure the role exists
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $user->role]);

            // Sync user's role with Spatie
            if (!$user->hasRole($user->role)) {
                $user->syncRoles([$user->role]);
            }
        }
    });
    }
}
