<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission_Answer extends Model
{
use HasFactory;


protected $fillable = ['submission_id','axis','q1','q2','q3','q4','axis_points','notes'];


protected $casts = [
'q1' => 'boolean', 'q2' => 'boolean', 'q3' => 'boolean', 'q4' => 'boolean',
];


public function submission() { return $this->belongsTo(Submission::class); }
}