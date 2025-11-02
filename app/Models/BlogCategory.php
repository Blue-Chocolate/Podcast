<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'header_image',
        'description',
        'image_path',
        'slug',
        'is_active',
        'views_count',
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'blog_category_id');
    }
}
