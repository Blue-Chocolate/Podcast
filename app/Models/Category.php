<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','description'];

    public function episodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_categories');
    }
public function blogs()
{
    return $this->hasMany(Blog::class, 'category_id');
}

}
