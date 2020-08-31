<?php

namespace App\Http\Controllers;

use App\AttendDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            // var_dump($request->all());return;
    
            // $request->validate([          
            //     '*.date' => 'required|date|date_format:Y-m-d',
            //     // '*.emp_no' => 'required|int',
            //     '*.total_hours' => 'numeric',
            //     // '*.pm2' => 'required',
            // ],
            // [
            //     '*.date.required'=> 'Date is Required', // custom message
            //     // '*.emp_no.required'=> 'First Name Should be Minimum of 8 Character', // custom message
            //     '*.total_hours.numeric'=> 'Total Hour is Required', // custom message
            //     // '*.pm2.required'=> 'Total pm is Required'
            // ]);            
  

            foreach($request->all() as $key => $val){
                 
                    $validator = Validator::make($val, [
                            'date' => 'required|date|date_format:Y-m-d',
                            // 'emp_no' => 'required|int',
                            'total_hours' => 'numeric',
                            'pm2' => 'numeric|nullable',
                    ],[
                        'date.required'=> 'Date is Required', // custom message
                        // '*.emp_no.required'=> 'First Name Should be Minimum of 8 Character', // custom message
                        'total_hours.numeric'=> 'Total Hour is Required', // custom message
                        'pm2.numeric'=> 'Total pm is Required'
                    ]);

                  
            }
            if ($validator->fails()) {
                return ['errors' => $validator->errors()->all()];                               
            } 

            $data=$request->all();
            // Model::insert($data);
            $attend=AttendDetail::insert($data);
            // $attend->save();
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
            $attend=AttendDetail::where('emp_no', $request->emp_no)->where('date','like',$date)->get();
            // var_dump($attend);
        }
      
       return $attend;
    }
}
