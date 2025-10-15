<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Organization extends Model
{
use HasFactory;

protected $fillable = [
    'name',
    'sector',
    'established_at',
    'email',
    'phone',
    'address',
    'strategy_plan_path',
    'financial_report_path',
    'structure_chart_path',
    'user_id',
];


protected $dates = ['established_at'];


public function submissions()
{
return $this->hasMany(Submission::class);
}
  
public function submission()
{
    return $this->hasOne(Submission::class)->latest();
}
public function user()
{
    return $this->belongsTo(User::class);
}

}