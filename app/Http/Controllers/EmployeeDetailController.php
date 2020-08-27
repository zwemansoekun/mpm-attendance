<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeeDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\Debugbar\Facade as Debugbar;

class EmployeeDetailController extends Controller
{

    public function findLastDataByEmployee($emp_id)
    {
        $employee = Employee::select("*")->where('emp_id',$emp_id)->first();

        $employeeDetail = new EmployeeDetail();
        if($employee != null)
        {
            $employeeDetail = EmployeeDetail::select("*")->where("employee_id",$employee->id)->orderBy('pay_month','desc')->with('employee')->first();
        }

        return response()->json($employeeDetail);
    }

    public function findByEmployee($emp_id)
    {
        $employeeDetails = EmployeeDetail::select("*")->where("employee_id",$emp_id)->orderBy('pay_month')->get();

        return response()->json($employeeDetails);
    }

    public function updateAll(Request $request)
    {
        //$ids = $request->id[0];
        //Debugbar::info($ids);
        
        foreach($request->all() as $detail){
            Debugbar::info('update**');
            Debugbar::info($detail);
            if(!array_key_exists("id" , $detail))
            {
                $new = new EmployeeDetail([
                    'pay_month' => $detail['pay_month'],
                    'salary_amount' => $detail['salary_amount'],
                    'trans_money' => $detail['trans_money'],
                    'jlpt' => $detail['jlpt'],
                    'ssb' => $detail['ssb'],
                    'position' => $detail['position'],
                    'address' => $detail['address'],
                    'phone_no' => $detail['phone_no'],
                    'nrc_no' => $detail['nrc_no'],
                    'bank_account' => $detail['bank_account'],
                    'member' => $detail['member'],
                    'child' => $detail['child'],
                    'emg_ph_no' => $detail['emg_ph_no'],
                    'waste_time' => $detail['waste_time'],
                    'employee_id' => $detail['employee_id']
                ]);
                $new->save();
            } else 
            {
                $oldDetail = EmployeeDetail::find($detail['id']);
                if($oldDetail->pay_month != $detail['pay_month'])
                {
                    $oldDetail->pay_month = $detail['pay_month'];
                }
                if($oldDetail->salary_amount != $detail['salary_amount'])
                {
                    $oldDetail->salary_amount = $detail['salary_amount'];
                }
                if($oldDetail->trans_money != $detail['trans_money'])
                {
                    $oldDetail->trans_money = $detail['trans_money'];
                }
                if($oldDetail->jlpt != $detail['jlpt'])
                {
                    $oldDetail->jlpt = $detail['jlpt'];
                }
                if($oldDetail->ssb != $detail['ssb'])
                {
                    $oldDetail->ssb = $detail['ssb'];
                }
                if($oldDetail->position != $detail['position'])
                {
                    $oldDetail->position = $detail['position'];
                }
                if($oldDetail->address != $detail['address'])
                {
                    $oldDetail->address = $detail['address'];
                }
                if($oldDetail->phone_no != $detail['phone_no'])
                {
                    $oldDetail->phone_no = $detail['phone_no'];
                }
                if($oldDetail->nrc_no != $detail['nrc_no'])
                {
                    $oldDetail->nrc_no = $detail['nrc_no'];
                }
                if($oldDetail->bank_account != $detail['bank_account'])
                {
                    $oldDetail->bank_account = $detail['bank_account'];
                }
                if($oldDetail->member != $detail['member'])
                {
                    $oldDetail->member = $detail['member'];
                }
                if($oldDetail->child != $detail['child'])
                {
                    $oldDetail->child = $detail['child'];
                }
                if($oldDetail->emg_ph_no != $detail['emg_ph_no'])
                {
                    $oldDetail->emg_ph_no = $detail['emg_ph_no'];
                }
                if($oldDetail->waste_time != $detail['waste_time'])
                {
                    $oldDetail->waste_time = $detail['waste_time'];
                }

                $oldDetail->save();

            }
            
        }
        return response()->json('update all');
    }
}
