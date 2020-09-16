<?php

namespace App\Http\Controllers;

use App\AttendDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AttendDetailsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\Debugbar\Facade as Debugbar;


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
        Debugbar::info($year);
 
         $attendTime = DB::select('select emp_no from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date', ['date' => $year]);
         $attendTime = json_decode(json_encode($attendTime),true);

         $empno = DB::select('select emp_id from employees');
         $empno = json_decode(json_encode($empno),true);

         $attendTimeArray = array();
         for ($i = 0; $i < count($attendTime); $i++) 
         {
             array_push($attendTimeArray, $attendTime[$i]['emp_no']);
         }

         $empnoArray = array();
         for ($i = 0; $i < count($empno); $i++) 
         {
             array_push($empnoArray, $empno[$i]['emp_id']);
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
         
    }

    /**
     * CSV出力する
     *
     */
    public function download($year)
    { 
        return Excel::download(new AttendDetailsExport($year), '勤怠管理表'.$year.'.xlsx');
    }
}
