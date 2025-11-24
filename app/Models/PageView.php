<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
        protected $connection = 'gnerals';   // ← deine gewünschte DB-Connection

    protected $fillable = ["dom",'url', 'referrer', 'ip', 'visited_at'];
    public $timestamps = false;
}
