<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendDetail extends Model
{
    protected $fillable = ['date','emp_no','total_hours','am1','am2','pm1','pm2','am_leave','pm_leave'];
    protected $casts = [
        'am1' => 'hh:mm',
        'am2' => 'hh:mm',
        'pm1' => 'hh:mm',
        'pm2' => 'hh:mm',
    ];
}
