<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['title','slug','description','created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function episodes()
    {
        return $this->belongsToMany(Episode::class, 'playlist_episodes')->withPivot('ord');
    }
}
