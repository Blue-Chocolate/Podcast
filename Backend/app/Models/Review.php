<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
use HasFactory;
protected $fillable = ['submission_id','judge_id','answers','total_points','comment'];
protected $casts = ['answers' => 'array'];


public function submission(){ return $this->belongsTo(Submission::class); }
public function judge(){ return $this->belongsTo(Judge::class); }
}