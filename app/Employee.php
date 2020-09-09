<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['kana_name', 'entry_date', 'dob', 'emp_id'];
    // protected $primaryKey = 'emp_id';

    // public function employeeDetails()
    // {
    //     return $this->hasMany('App\EmployeeDetail');
    // }

    public function salarys()
    {
        return $this->hasMany('App\Salary');
    }

    public function employeeDetails()
    {
        // return $this->belongsTo('App\EmployeeDetail','emp_id');
        return $this->hasMany('App\EmployeeDetail','emp_id','emp_id');
    }
}
