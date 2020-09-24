<?php

namespace App\Http\Controllers;



use App\Ssb;
use App\Salary;
use App\Setting;
use App\Employee;
use App\AttendDetail;

use App\EmployeeDetail;
use Illuminate\Http\Request;

use App\Exports\PaySlipExport;
use App\Exports\InvoicesExport;
use App\Exports\PaySlipsExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Support\Facades\Validator;
use App\Exports\EngineerExport;

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
        $status1=0;$status2=0;    
        if($request->has('form1')){
            foreach($request->form1 as $key => $val){
                if(isset($val['id']) && $val['id']!='' ){
                    $status1=1;
                }

                $validator = Validator::make($val, [
                        'pay_month' => 'required',
                        'income' => 'required|numeric',
                        'total_salary' => 'required|numeric',
                        'trans_money' => 'numeric|nullable',
                        'jlpt'=> 'numeric|nullable',
                        'bonus'=> 'numeric|nullable',
                        'payment_amount'=>'required|numeric',
                        'income_tax'=>'required|numeric',
                        'ssb'=>'numeric|nullable',
                        'leave_late'=>'numeric|nullable',
                        'employee_id'=>'required|numeric',
                ],[
                    'income.required'=> '基本給は必要です。', // custom message
                    'income.numeric'=> '基本給をチェックして下さい！', // custom message
                    'total_salary.required'=> '合計は必要です。', // custom message
                    'total_salary.numeric'=> '合計をチェックして下さい！', // custom message
                    'trans_money.numeric'=> '通勤交通費をチェックして下さい！', 
                    'jlpt.numeric'=> 'JLPTをチェックして下さい！',
                    'bonus.numeric'=> 'ボーナスをチェックして下さい！',
                    'payment_amount.required'=> '支給額は必要です。',
                    'payment_amount.numeric'=> '支給額をチェックして下さい！', // custom message
                    'income_tax.required'=> '所得税は必要です。',
                    'income_tax.numeric'=> '所得税をチェックして下さい！', // custom message
                    'ssb.numeric'=> 'SSBをチェックして下さい！',
                    'leave_late.numeric'=> '遅刻欠勤早退をチェックして下さい！',
                ]);
                if ($validator->fails()) {
                    return ['errors' => $validator->errors()->all()];                               
                }
            }
        }

        if($request->has('form2')){
            foreach($request->form2 as $key => $val){

                if(isset($val['id']) && $val['id']!='' ){
                    $status2=1;
                }
                    $validator = Validator::make($val, [
                        'total_amount' => 'required|numeric',
                        'c_paid' => 'required|numeric',
                        'remark'=> 'nullable',
                ],[
                    'total_amount.required'=> '総額は必要です。', // custom message
                    'total_amount.numeric'=> '総額をチェックして下さい！', // custom message
                    'c_paid.required'=> '会社負担分は必要です。', // custom message
                    'c_paid.numeric'=> '会社負担分をチェックして下さい！', // custom message
                ]);
                if ($validator->fails()) {
                    return ['errors' => $validator->errors()->all()];                               
                }
            }
        }
        
        // $data1=$request->form1;
        $data1=$request->form1;
        $data2=$request->form2;
        $datas1='';$datas2='';

            if($status1==1 || $status2==1){

                foreach($data1 as $key => $val){

                    $datas1=Salary::where('id','=',$val['id'])
                                        ->where('pay_month','=',$val['pay_month'])
                                        ->where('employee_id','=',$val['employee_id'])  
                                        ->update([
                                            'income' => $val['income'],
                                            'trans_money' =>$val['trans_money'], 
                                            'jlpt' =>$val['jlpt'], 
                                            'bonus' =>$val['bonus'], 
                                            'income_tax' =>$val['income_tax'], 
                                            'ssb' =>$val['ssb'], 
                                            'total_salary' =>$val['total_salary'], 
                                            'leave_late' =>$val['leave_late'], 
                                            'payment_amount' =>$val['payment_amount'] 
                                        ]); 
                };

                foreach($data2 as $key => $val){

                    $datas2=Ssb::where('id','=',$val['id'])
                                        ->update([
                                            'total_amount' => $val['total_amount'],
                                            'c_paid' =>$val['c_paid'], 
                                            'remark' =>$val['remark'], 
                                        ]); 
                };
                    
            }else{
                DB::beginTransaction();
                try {
                    $datas2=Ssb::insert($data2);
                    $ssbid=DB::getPdo()->lastInsertId();
                    if($ssbid){
                        for($i=0;$i<count($data1);$i++){
                            $data1[$i]['ssb_id']=$ssbid++;
                        }
                        $datas1=Salary::insert($data1);
                    }
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                }
               
            }

            if($datas1 && $datas2){
                return ['message'=>true];
            }else{
                return ["message"=>false];
            }
    
            
        


    
        //  return count($data1);   
        // DB::beginTransaction();
        // try {
        //     $datas2=Ssb::insert($data2);
        //     $ssbid=DB::getPdo()->lastInsertId();
        //     if($ssbid){
        //         for($i=0;$i<count($data1);$i++){
        //             $data1[$i]['ssb_id']=$ssbid++;
        //         }
        //         $datas1=Salary::insert($data1);
        //     }
        //     DB::commit();
        // } catch (Exception $e) {
        //     DB::rollBack();
        // }
       


       
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
        $adetailcount=0;
        $ym=explode("-",$yearmonth);  
        $salary=EmployeeDetail::with('employee')->where('pay_month',str_replace("-","/",$yearmonth))->get();//->toArray();//with('employee')->
      
         $daycount=cal_days_in_month(CAL_GREGORIAN,$ym[1],$ym[0]);

        DB::beginTransaction();
        try {
            for($i=0;$i<count($salary);$i++){
              
            
    
                    
                $adetailcount=AttendDetail::whereYear('date',$ym[0])
                ->whereMonth('date',$ym[1])
                ->where('emp_no',$salary[$i]['emp_id'])
                ->where(function($q) {
                    $q->orWhereNotNull('am1')
                    ->orWhereNotNull('am2')
                    ->orWhereNotNull('pm1')
                    ->orWhereNotNull('pm2');
                })->count();
    
                $adetail_lateleave=AttendDetail::whereYear('date',$ym[0])
                ->whereMonth('date',$ym[1])
                ->where('emp_no',$salary[$i]['emp_id'])
                ->get();
                $office_day=$adetail_lateleave->count();
                $office_hour=$office_day*8;
                $sum_hour=$adetail_lateleave->sum('total_hours');
                $leave_attend_hr=$office_hour-$sum_hour;
                
                $oneday=$salary[$i]['salary_amount']/$daycount;
                $salary[$i]['trans_money']= $salary[$i]['trans_money']*$adetailcount;
                $salary[$i]['late_leave_money']=round(number_format((float)(($leave_attend_hr/8)*$oneday), 2, '.', '')); 
            } 
        } catch (Exception $e) {
            DB::rollBack();
        }
        
        $res_salary= EmployeeDetailResource::collection($salary);            

        return $res_salary;
      
    }

    public function excel_export($yearMonth , $employees){

        $arrEmp = array();
        $myArray = explode(',', $employees);
        
        foreach($myArray as $my_Array){
            $employee = Employee::select('*')->where('emp_id',$my_Array)->first();
            array_push($arrEmp , $employee);
        }

        $filename = '給与明細_'. now()->format('YmdHis').'.xlsx';
        return (new PaySlipsExport($yearMonth,$arrEmp))->download($filename);
    }

    public function getsalary(Request $request)
    {
        $salary='';
        if($request->pay_month){
            $salary=Salary::with('ssbval')->where('pay_month',$request->pay_month)->get()->toArray();
            // var_dump($attend);
        }
      
       return $salary;

    }
    // public function ssb(){
    //         $ssb=Ssb::get();
    //         return $ssb;
    // }

    public function download($yearmonth)
    {  
        // $global=config('global');
        $yearMonth=str_replace('-','/',$yearmonth);
        // $setting='';
        //  $employdetail=EmployeeDetail::with('employee')->where('pay_month',$yearMonth)->get();//->toArray();//with('employee')->
        //  $setting=Setting::select('money')->where('create_month','=',$yearMonth)->get();
        // return !$setting->isEmpty();
        //  if($setting->isEmpty()){          
        //      $setting=$global;
        //  }
        //  return $setting[0]['money'];

        // $ym=explode("-",$yearmonth); 
        // $adetail=AttendDetail::whereYear('date',2020)
        //        ->whereMonth('date',07)
        // ->where(function($q) {
        //     $q->orWhereNotNull('am1')
        //       ->orWhereNotNull('am2')
        //       ->orWhereNotNull('pm1')
        //       ->orWhereNotNull('pm2');
        // })->count();

        // return $adetail;
        
         if($yearMonth){    
            libxml_use_internal_errors(true);      
            return (new EngineerExport($yearMonth))->download('海外エンジニアコスト一覧表_'.$yearmonth.'.xlsx');
         }
        
        // return Excel::download(new EngineerExport($yearmonth), '海外エンジニアコスト一覧表_'.$yearmonth.'.xlsx');
    }

}
