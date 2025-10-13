<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
use HasFactory;


protected $fillable = ['submission_id','axis','original_name','path','mime_type','size','uploaded_by'];


public function submission() { return $this->belongsTo(Submission::class); }
}