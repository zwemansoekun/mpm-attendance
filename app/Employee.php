<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['kana_name', 'entry_date', 'dob', 'emp_id'];

    // public function employeeDetails()
    // {
    //     return $this->hasMany('App\EmployeeDetail');
    // }

    public function salarys()
    {
        return $this->hasMany('App\Salary');
    }
}
