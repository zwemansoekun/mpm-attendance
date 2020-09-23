<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultSetting extends Model
{
    protected $fillable = ['ssb_max','ssb_paid','money'];
    protected $hidden = ["created_at", "updated_at"];
}
