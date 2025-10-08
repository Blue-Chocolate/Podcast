<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password_hash','role','avatar_url'];

    protected $hidden = ['password_hash'];

    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'created_by');
    }
}
