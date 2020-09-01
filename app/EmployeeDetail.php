<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    protected $fillable = ['pay_month', 'salary_amount' , 'trans_money', 'jlpt','ssb', 'position','address','phone_no','nrc_no','bank_account','member','child',
                            'emg_ph_no' , 'waste_time','emp_id'];

    // public function employee()
    // {
    //     return $this->belongsTo('App\Employee');
    // }
}
