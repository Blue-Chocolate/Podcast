<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCategory extends Model
{
    protected $table = 'video_categories';

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'slug',
        'is_active',
        'views_count',
    ];

     public function videos()
    {
        return $this->hasMany(Video::class, 'video_category_id');
    }
}
