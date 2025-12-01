<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'views_count',
        'video_category_id',
        'image_path',
        'video_path',
    ];

    // علاقة مع التصنيف
    public function category()
{
      return $this->belongsTo(VideoCategory::class, 'video_category_id');
}
    
}
