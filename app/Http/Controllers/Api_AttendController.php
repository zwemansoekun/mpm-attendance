<?php

namespace App\Http\Controllers;

use App\Api_Attend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Api_AttendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {          
        return Api_Attend::select(DB::raw('DATE_FORMAT(recordedDateTime,"%Y/%m") as recordedDateTime'))->distinct()->orderBy('id','desc')->get()->toArray();//all()->toArray(); 
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Api_Attend  $api_Attend
     * @return \Illuminate\Http\Response
     */
    public function show($emp_no,$date)
    {
        return Api_Attend::select(DB::raw('DATE_FORMAT(recordedDateTime,"%Y/%m/%d") as recordedDateTime'),
                                  DB::raw('DATE_FORMAT(recordedDateTime,"%H:%i") as am_pm'),
                                  DB::raw('DATE_FORMAT(recordedDateTime,"%H") as hour'),
                                  DB::raw('DATE_FORMAT(recordedDateTime,"%i") as minute'))
                         ->where('employeeId',$emp_no)   
                         ->where(DB::raw('EXTRACT(YEAR_MONTH from recordedDateTime)'),$date)         
                         ->orderBy(DB::raw('DATE(recordedDateTime)'))
                         ->orderBy(DB::raw('DATE_FORMAT(recordedDateTime,"%H:%i")'))
                         ->get()->toArray();//all()->toArray(); 
    }
}
