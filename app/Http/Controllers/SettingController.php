<?php

namespace App\Http\Controllers;

use App\Setting;
use App\DelayTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\Debugbar\Facade as Debugbar;

class SettingController extends Controller
{
    public function index(){
        $setting = Setting::orderBy('create_month','desc')->first();
        return response()->json($setting);
    }

    public function all(){
        $settings = Setting::all();
        return response()->json($settings);
    }

    public function updateMoney($id ,Request $request){
        $setting = Setting::find($id);
        
        $now = Carbon::now();
        $monthString = $now->format("yy/m");
        if($monthString == $setting->create_month){
            $setting->money = $request->input('money');
            $setting->save();
        }else {
            $new = new Setting([
                'am' => $setting->am,
                'pm' => $setting->pm,
                'money' => $request->input('money'),
                'create_month' => $monthString
            ]);
            $new->save();
        }
        
        return response()->json(Setting::orderBy('create_month','desc')->first());
    }

    public function updateAm($id ,Request $request){
        $setting = Setting::find($id);
        
        $now = Carbon::now();
        $monthString = $now->format("yy/m");
        if($monthString == $setting->create_month){
            $setting->am = $request->input('am');
            $setting->save();
        }else {
            $new = new Setting([
                'am' => $request->input('am'),
                'pm' => $setting->pm,
                'money' => $setting->money,
                'create_month' => $monthString
            ]);
            $new->save();
        }
        
        return response()->json(Setting::orderBy('create_month','desc')->first());
    }

    public function updatePm($id ,Request $request){
        $setting = Setting::find($id);
        
        $now = Carbon::now();
        $monthString = $now->format("yy/m");
        if($monthString == $setting->create_month){
            $setting->pm = $request->input('pm');
            $setting->save();
        }else {
            $new = new Setting([
                'am' => $setting->am,
                'pm' => $request->input('pm'),
                'money' => $setting->money,
                'create_month' => $monthString
            ]);
            $new->save();
        }
        
        return response()->json(Setting::orderBy('create_month','desc')->first());
    }
    
    public function delayTime(Request $request){

        $get_ampm=DelayTime::select("*")->where("month",'like', '%' . $request->route('year') .'/'. $request->route('month') . '%')->first();      
        return $get_ampm !== null ? $get_ampm : [] ;//Route::current()->parameters('month')
    }

}
