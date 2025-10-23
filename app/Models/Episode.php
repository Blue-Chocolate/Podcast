<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Episode extends Model
{
    protected $fillable = [
        'podcast_id', 'season_id', 'episode_number', 'title', 'slug',
        'description', 'short_description', 'duration_seconds', 'explicit',
        'status', 'published_at', 'cover_image', 'video_url', 'audio_url',
        'file_size', 'mime_type', 'views_count'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'explicit' => 'boolean',
    ];

    // Don't append anything - let Filament handle raw values
    protected $appends = [];

    /**
     * Fix duplicated path in video_url and return clean path
     */
    public function getVideoUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove duplicate 'episodes/' if exists
        return preg_replace('#episodes/episodes/#', 'episodes/', $value);
    }

    /**
     * Fix duplicated path in audio_url and return clean path
     */
    public function getAudioUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove duplicate 'episodes/' if exists
        return preg_replace('#episodes/episodes/#', 'episodes/', $value);
    }

    /**
     * Get full public URL for video
     */
    public function getVideoFullUrlAttribute()
    {
        if (empty($this->video_url)) {
            return null;
        }
        
        // If already a full URL, return as-is
        if (filter_var($this->video_url, FILTER_VALIDATE_URL)) {
            return $this->video_url;
        }
        
        // Use Storage facade to generate correct URL
        return Storage::disk('public')->url($this->video_url);
    }

    /**
     * Get full public URL for audio
     */
    public function getAudioFullUrlAttribute()
    {
        if (empty($this->audio_url)) {
            return null;
        }
        
        // If already a full URL, return as-is
        if (filter_var($this->audio_url, FILTER_VALIDATE_URL)) {
            return $this->audio_url;
        }
        
        // Use Storage facade to generate correct URL
        return Storage::disk('public')->url($this->audio_url);
    }

    /**
     * Get full public URL for cover image
     */
    public function getCoverImageFullUrlAttribute()
    {
        if (empty($this->cover_image)) {
            return null;
        }
        
        // If already a full URL, return as-is
        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            return $this->cover_image;
        }
        
        // Use Storage facade to generate correct URL
        return Storage::disk('public')->url($this->cover_image);
    }

    // Relationships
    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}