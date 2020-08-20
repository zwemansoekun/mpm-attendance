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

    public function saveAm($id ,Request $request){
      
        $delayTime = DelayTime::find($id);

        $delayTime->am = $request->am;
        $delayTime->save();
        
        $delayTimes = DelayTime::orderBy('month')->get(); 
        return response()->json($delayTimes);
    }

    public function savePm($id ,Request $request){
      
        $delayTime = DelayTime::find($id);
        
        $delayTime->pm = $request->pm;
        $delayTime->save();
        
        $delayTimes = DelayTime::orderBy('month')->get(); 
        return response()->json($delayTimes);
    }
}
