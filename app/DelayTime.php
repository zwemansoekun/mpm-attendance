<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelayTime extends Model
{
    protected $fillable = ['month', 'am' , 'pm','money'];
}
