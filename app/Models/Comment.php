<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['episode_id','user_name','user_email','content','approved'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
