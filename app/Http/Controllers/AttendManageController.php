<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facade as Debugbar;
use App\AttendDetail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendDetailsExport;
use Illuminate\Support\Facades\DB;

class AttendManageController extends Controller
{
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
    public function csvOutput($year)
    {
        Debugbar::info($year);
 
         $attendTime = DB::select('select * from attend_details where EXTRACT(YEAR_MONTH FROM date) = :date', ['date' => $year]);
      
         return response()->json($attendTime);
    }

    /**
     * CSV出力する
     *
     */
    public function download($year)
    {
        return Excel::download(new AttendDetailsExport($year), 'users.xlsx');
    }

}
