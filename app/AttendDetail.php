<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendDetail extends Model
{
    protected $fillable = ['date','emp_no','total_hours','am1','am2','pm1','pm2','am_leave','pm_leave'];
    protected $hidden = ["created_at", "updated_at"];
    protected $casts = [
        'am1' => 'datetime:H:i',
        'am2' => 'datetime:H:i',
        'pm1' => 'datetime:H:i',
        'pm2' => 'datetime:H:i',
    ];
}
