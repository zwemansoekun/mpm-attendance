<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ssb extends Model
{
    protected $fillable = ['id','total_amount', 'c_paid' , 'remark'];
    protected $hidden = ["created_at", "updated_at"];

    public function salary()
    {
        return $this->belongsTo('App\Salary','ssb_id','id');
    }
}
