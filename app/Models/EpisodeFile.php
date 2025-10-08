<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpisodeFile extends Model
{
    use HasFactory;

    protected $fillable = ['episode_id','file_url','mime_type','file_size_bytes','bitrate_kbps','format'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
