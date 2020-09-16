<?php

namespace App\Http\Controllers;

use App\AttendDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\Debugbar\Facade as Debugbar;
use App\Exports\AttendForMonthExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendController extends Controller
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
            
            $status=0;    
            foreach($request->all() as $key => $val){
                 
                    if(isset($val['id']) && $val['id']!='' ){
                        $status=1;
                    }
                 
                    $validator = Validator::make($val, [
                            'date' => 'required|date|date_format:Y-m-d',
                            'total_hours' => 'numeric|nullable',
                            'am1'=>'date_format:H:i|nullable',
                            'am2'=>'date_format:H:i|nullable',
                            'pm1'=>'date_format:H:i|nullable',
                            'pm2'=>'date_format:H:i|nullable',
                            'am_leave'=>'numeric|nullable',
                            'pm_leave'=>'numeric|nullable',
                    ],[
                        'date.required'=> '日付は必要です。', // custom message
                        'date.date_format'=> '日付の形式をチェックして下さい！', // custom message
                        'total_hours.numeric'=> '合計時間をチェックして下さい！', 
                        'am1.date_format'=> 'AM1をチェックして下さい！',
                        'am2.date_format'=> 'AM2をチェックして下さい！',
                        'pm1.date_format'=> 'PM1をチェックして下さい！',
                        'pm2.date_format'=> 'PM2をチェックして下さい！',
                        'am_leave.numeric'=> 'AMの（欠勤/有休）をチェックして下さい！',
                        'pm_leave.numeric'=> 'PMの（欠勤/有休）をチェックして下さい！',
                    ]);
                    if ($validator->fails()) {
                        return ['errors' => $validator->errors()->all()];                               
                    }
            }
            $data=$request->all();
         
            if($status==1){

                foreach($data as $key => $val){

                    $attend=AttendDetail::where('id','=',$val['id'])
                                        ->where('date','like','%'.$val['date'].'%')
                                        ->where('emp_no','=',$val['emp_no'])  
                                        ->update([
                                            'date' => $val['date'],
                                            'emp_no' =>$val['emp_no'], 
                                            'total_hours' =>$val['total_hours'], 
                                            'am1' =>$val['am1'], 
                                            'am2' =>$val['am2'], 
                                            'pm1' =>$val['pm1'], 
                                            'pm2' =>$val['pm2'], 
                                            'am_leave' =>$val['am_leave'], 
                                            'pm_leave' =>$val['pm_leave'] 
                                        ]);                              
                            

                };
                    
            }else{
                    $attend=AttendDetail::insert($data);
            }

            if($attend){
                return ['message'=>true];
            }else{
                return ["message"=>false];
            }
    
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

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function getmonth(Request $request)
    {
        $attend='';
        if($request->year && $request->month){
            $date='%'.$request->year."-".$request->month.'%'; 
            $attend=AttendDetail::where('emp_no', $request->emp_no)->where('date','like',$date)->get()->toArray();
            // var_dump($attend);
        }
      
       return $attend;
    }

    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function csvOutput($employee,$date)
    {
        return Excel::download(new AttendForMonthExport($employee,$date), '出勤簿生成.xlsx');
    }
}
