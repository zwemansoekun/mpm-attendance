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

    public function save($emp_id ,Request $request){
        $employee = Employee::select("*")->where('emp_id',$emp_id)->first();
        if($employee == null){
            $employee = new Employee([
                'kana_name' => $request->input('kana_name'),
                'entry_date' => $request->input('entry_date'),
                'dob' => $request->input('dob'),
                'emp_id' => $emp_id
            ]);
            
        } else {
            $employee->kana_name = $request->input('kana_name');
            $employee->entry_date = $request->input('entry_date');
            $employee->dob = $request->input('dob');
        }

        $employee->save();
        return response()->json('Employee successfully added');
    }
    
}
