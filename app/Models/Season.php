<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'podcast_id',
        'number',
        'title',
        'description',
        'release_date',
    ];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    // 🔗 Each season has many episodes
    public function episodes()
    {
        return $this->hasMany(Episode::class, 'season_id')
            ->orderBy('episode_number');
    }
    
}
