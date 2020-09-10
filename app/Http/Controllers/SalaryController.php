<?php

namespace App\Http\Controllers;


use App\Salary;
use App\Employee;
use App\EmployeeDetail;
use Illuminate\Http\Request;
use App\Exports\PaySlipExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\EmployeeDetail as EmployeeDetailResource;


class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function list($yearmonth)
    {
           
        $salary=EmployeeDetail::with('employee')->where('pay_month',str_replace("-","/",$yearmonth))->get();//->toArray();//with('employee')->
      
        $res_salary= EmployeeDetailResource::collection($salary);
        return $res_salary;
      
    }

    public function csv_export(){
        // var_dump('aye');
        $year = '2019/05';
        $id = 6;
        $employee = Employee::select('*')->where('emp_id',$id)->first();
        $salary = Salary::select("*")->where("pay_month",$year)->where('employee_id',$employee->id)->first();
        return Excel::download(new PaySlipExport($employee,$salary,'001','may wathone'), 'testing.xlsx');
    }
}
