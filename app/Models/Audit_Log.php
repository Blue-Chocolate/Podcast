<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit_Log extends Model
{
    use HasFactory;

    protected $table = 'audit_logs'; // <- هنا تحدد اسم الجدول بالظبط اللي في DB
    protected $fillable = ['entity_type','entity_id','action','user_id','notes'];
}

