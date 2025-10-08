<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

   protected $fillable = [
    'podcast_id', 'season_id', 'title', 'slug', 'description',
    'short_description', 'duration_seconds', 'status', 'cover_image',
    'transcript_id', 'audio_url', 'video_url', 'file_size', 'mime_type', 'published_at'
];

    protected $dates = ['published_at'];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function transcript()
    {
        return $this->hasOne(Transcript::class);
    }

    public function files()
    {
        return $this->hasMany(EpisodeFile::class);
    }

    public function hosts()
    {
        return $this->belongsToMany(Person::class, 'episode_hosts');
    }

    public function guests()
    {
        return $this->belongsToMany(Person::class, 'episode_guests')->withPivot('role');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'episode_categories');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'episode_tags');
    }

    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'episode_sponsors')->withPivot('position');
    }

    public function plays()
    {
        return $this->hasMany(Play::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_episodes')->withPivot('ord');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
