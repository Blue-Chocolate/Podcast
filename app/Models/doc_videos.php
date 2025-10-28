<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocVideo extends Model
{
    protected $fillable = [
        'title',
        'description',
        'views_count',
        'category_id',
        'image_path',
        'video_path',
    ];

    // علاقة مع التصنيف
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
