<?php

namespace App\Http\Controllers;

use App\AttendDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AttendDetailsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use App\Api_Employees;
use Illuminate\Support\Str;
use Barryvdh\Debugbar\Facade as Debugbar;




class AttendManageController extends Controller
{
    public $attendTime = null;
    public  $empArray = array();
    public $csvArray = array();
    public $length = null;
    public $emp = null;
    //public $found = array();

  
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
        
        $attendTime = DB::select('select emp_no from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date order by emp_no', ['date' => $year]);
        $attendTime = json_decode(json_encode($attendTime),true);
      
        $empno = file_get_contents(env('MIX_APP_AungThiHa_URL')."/employees");
     
        $empnoArray1 = json_decode($empno, true);
        
        $attendTimeArray = array();
         
        for ($i = 0; $i < count($attendTime); $i++) 
        {
            if(!in_array($attendTime[$i]['emp_no'],$attendTimeArray)){array_push($attendTimeArray, $attendTime[$i]['emp_no']);}
        }
       

        $empnoArray = array();
         
         for ($i = 0; $i < count($empnoArray1); $i++) 
         {
             array_push($empnoArray, $empnoArray1[$i]['id']);
         }
        
         $empLocalArr = array();
         for($i =0; $i< count($attendTimeArray); $i++)
         {
            $empLocal = array();
            $empLocal = DB::select('select emp_id from employees where emp_id =:emp_id' , 
            ['emp_id' => $attendTimeArray[$i]]);
            $empLocal = json_decode(json_encode($empLocal),true);
            if($empLocal != null)
            {
                array_push($empLocalArr,  $empLocal);
            }
           
        }
     
         $found = array();
         foreach($attendTimeArray as $num) 
         {
            if (in_array($num,$empnoArray)) 
            {
                array_push($found, 0);
            }else
            {
                array_push($found, 1);
            }
        }
     
        if(in_array(0,$found) && count($empLocalArr) != 0)
        {
            return response()->json("success");
        }else
        {
            return response()->json("fail");
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
