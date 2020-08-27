<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendDetail extends Model
{
    protected $casts = [
        'am1' => 'hh:mm',
        'am2' => 'hh:mm',
        'pm1' => 'hh:mm',
        'pm2' => 'hh:mm',
    ];
}
