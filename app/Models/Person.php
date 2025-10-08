<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','role','bio','avatar_url','website','social_json'];

    protected $casts = [
        'social_json' => 'array',
    ];

    public function hostedEpisodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_hosts');
    }

    public function guestEpisodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_guests')->withPivot('role');
    }
}
