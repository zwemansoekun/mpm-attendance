<?php

namespace App\Http\Controllers;

use App\Setting;
use App\DelayTime;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facade as Debugbar;
use Carbon\Carbon;

class DelayTimeController extends Controller
{
    public function index(){
        $delayTimes = DelayTime::orderBy('month')->get(); 
        return response()->json($delayTimes);
    }

    public function store(Request $request){
        $now = Carbon::now();
        $monthString = $now->format("yy/m");

        $setting = Setting::find(1);    
        $dTime = DelayTime::select("*")->where("month",$monthString)->first();

        if($dTime == null){
       
            $delayTime = new DelayTime([
                'month' => $monthString,
                'am' => $setting->am,
                'pm' => $setting->pm,
                'money' => $setting->money
            ]);
            $delayTime->save();
        }
 
        return response()->json("add");
    }

    public function saveAm($id ,Request $request){
      
        $delayTime = DelayTime::find($id);

        $delayTime->am = $request->am;
        $delayTime->save();
        
        $delayTimes = DelayTime::all(); 
        return response()->json($delayTimes);
    }

    public function savePm($id ,Request $request){
      
        $delayTime = DelayTime::find($id);
        
        $delayTime->pm = $request->pm;
        $delayTime->save();
        
        $delayTimes = DelayTime::all(); 
        return response()->json($delayTimes);
    }
}
