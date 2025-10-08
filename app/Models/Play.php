<?php 


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    use HasFactory;

    protected $fillable = ['episode_id','played_at','ip_address','user_agent','referrer','duration_listened_seconds'];

    protected $dates = ['played_at'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
