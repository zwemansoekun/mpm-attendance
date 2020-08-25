<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'pay_month', 'income', 'trans_money', 'jlpt' , 'bonus', 'income_tax','ssb' , 'leave_late', 'payment_amount'
    ];

    public function Ssb()
    {
        return $this->belongsTo('App\Ssb');
    }
}
