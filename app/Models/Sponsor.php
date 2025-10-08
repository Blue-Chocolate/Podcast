<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = ['name','logo_url','website','description'];

    public function episodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_sponsors')->withPivot('position');
    }
}
