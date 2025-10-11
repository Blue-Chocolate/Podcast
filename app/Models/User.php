<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;

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

}
