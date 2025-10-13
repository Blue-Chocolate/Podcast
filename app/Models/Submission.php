<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Submission extends Model
{
use HasFactory;


protected $fillable = ['organization_id','status','total_score','submitted_at','announced_at','meta'];


protected $casts = [
'meta' => 'array',
'submitted_at' => 'datetime',
'announced_at' => 'datetime',
'total_score' => 'decimal:2',
];


public function organization() { return $this->belongsTo(Organization::class); }
public function answers() { return $this->hasMany(Submission_Answer::class); }
public function attachments() { return $this->hasMany(Attachment::class); }
public function reviews() { return $this->hasMany(Review::class); }
}