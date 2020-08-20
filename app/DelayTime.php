<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelayTime extends Model
{
    protected $fillable = ['month', 'am' , 'pm','money'];
    protected $hidden = ["created_at", "updated_at"];
}
