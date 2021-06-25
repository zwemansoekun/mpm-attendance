<?php

namespace App\Http\Controllers;

use App\AttendDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AttendDetailsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use App\Api_Employees;




class AttendManageController extends Controller
{
    public $attendTime = null;
    public  $empArray = array();
    public $csvArray = array();
  
    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('attendManage');
    }

    /**
     * CSVデータ出力する
     *
     */
    public static function csvOutput($year)
    {
        
 
         $attendTime = DB::select('select emp_no from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date', ['date' => $year]);
         $attendTime = json_decode(json_encode($attendTime),true);
      
        //$empno = DB::select('select emp_id from employees');
         $empno =  file_get_contents(env('MIX_APP_AungThiHa_URL')."/employees");
     
         $empnoArray1 = json_decode($empno, true);
        
         
         if(count($attendTime) == 0 || count($empnoArray1) == 0 )
         {
            return response()->json("fail");
         }


         $attendTimeArray = array();
         for ($i = 0; $i < count($attendTime); $i++) 
         {
             array_push($attendTimeArray, $attendTime[$i]['emp_no']);
         }

        
         $empnoArray = array();
         for ($i = 0; $i < count($empnoArray1); $i++) 
         {
             array_push($empnoArray, $empnoArray1[$i]['employeeId']);
         }

        $found = array();
         foreach($attendTimeArray as $num) {
            if (in_array($num,$empnoArray)) {
                array_push($found, true);
            }else{
                array_push($found, false);
            }
        }
    

         if(in_array(false,$found))
         {
            return response()->json("fail");
         }
         else
         {
            return response()->json("success");
         }

         $found = array();
         foreach($empnoArray as $num) {
            if (in_array($num,$attendTimeArray)) {
                array_push($found, true);
            }else{
                array_push($found, false);
            }
        }
        
        if(in_array(false,$found))
        {
           return response()->json("fail");
        }
        else
        {
           return response()->json("success");
        }
         
    }

    /**
     * CSV出力する
     *
     */
    public function download($year)
    { 
        return Excel::download(new AttendDetailsExport($year), '勤怠管理表'.$year.'.xlsx');
        //return null;
    }
}
