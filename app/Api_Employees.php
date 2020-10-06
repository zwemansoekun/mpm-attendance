<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Api_Employees extends Model
{ 
    protected $table = 'api_employees'; 
    protected $hidden = ["created_at", "updated_at"];

    protected $casts = [
        'basicSalary' => 'integer',
    ];
}
