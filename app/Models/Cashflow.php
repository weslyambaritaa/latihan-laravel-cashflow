<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    protected $table = 'cashflow';
    protected $fillable = ['user_id', 'title', 'description', 'tipe', 'cover'];
    public $timestamps = true;
}

