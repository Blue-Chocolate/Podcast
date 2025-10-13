<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Judge extends Model
{
use HasFactory;
protected $fillable = ['name','email','role'];


public function reviews(){ return $this->hasMany(Review::class); }
}
