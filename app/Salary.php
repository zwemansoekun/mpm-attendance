<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'pay_month', 'income', 'trans_money', 'jlpt' , 'bonus', 'income_tax','ssb' ,'total_salary ', 'leave_late', 'payment_amount','employee_id','ssb_id','adju1','adju2','adju3',
    ];
    protected $hidden = ["created_at", "updated_at"];

    public function ssbval()
    {
        return $this->hasOne('App\Ssb','id','ssb_id');
    }
}
