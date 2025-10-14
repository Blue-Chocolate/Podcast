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
        'file_size', 'mime_type'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'explicit' => 'boolean',
    ];

    // Add these to prevent accessor conflicts with Filament
    protected $appends = [];

    /**
     * Get the full URL for the video
     */
    public function getVideoUrlFullAttribute()
    {
        if (empty($this->attributes['video_url'])) {
            return null;
        }
        
        $value = $this->attributes['video_url'];
        
        // If it's already a full URL, return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Generate URL using asset helper for public storage
        return asset('storage/episodes/' . $value);
    }

    /**
     * Get the full URL for the audio
     */
    public function getAudioUrlFullAttribute()
    {
        if (empty($this->attributes['audio_url'])) {
            return null;
        }
        
        $value = $this->attributes['audio_url'];
        
        // If it's already a full URL, return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Generate URL using asset helper for public storage
        return asset('storage/episodes/' . $value);
    }

    /**
     * Get the full URL for the cover image
     */
    public function getCoverImageFullAttribute()
    {
        if (empty($this->attributes['cover_image'])) {
            return null;
        }
        
        $value = $this->attributes['cover_image'];
        
        // If it's already a full URL, return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Generate URL using asset helper for public storage
        return asset('storage/episodes/' . $value);
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