<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Api_Attend extends Model
{
    protected $table = 'api_attendances'; 
    protected $hidden = ["created_at", "updated_at"]; 
}
