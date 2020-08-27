<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\Debugbar\Facade as Debugbar;

class EmployeeController extends Controller
{

    public function findByEmployee($emp_id)
    {
        $employee = Employee::select("*")->where('emp_id',$emp_id)->first();

        return response()->json($employee);
    }
    
}
