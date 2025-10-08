<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = ['episode_id','transcript_text','transcript_file_url','language'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
