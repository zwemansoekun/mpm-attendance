<?php

namespace App\Http\Controllers;

use App\Setting;
use App\DelayTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Barryvdh\Debugbar\Facade as Debugbar;

class SettingController extends Controller
{
    public function index(){
        $setting = Setting::find(1);
        return response()->json($setting);
    }

    public function update(Request $request){
        $setting = Setting::find(1);
        $setting->money = $request->input('money');
        $setting->save();
        return response()->json($setting);
    }

    public function updateAm(Request $request){
        $setting = Setting::find(1);
        $setting->am = $request->input('am');
        $setting->save();
        return response()->json($setting);
    }

    public function updatePm(Request $request){
        $setting = Setting::find(1);
        $setting->pm = $request->input('pm');
        $setting->save();
        return response()->json($setting);
    }

    public function delayTime(Request $request){

        $get_ampm=DelayTime::select("*")->where("month",'like', '%' . $request->route('year') .'/'. $request->route('month') . '%')->first();      
        return $get_ampm !== null ? $get_ampm : [] ;//Route::current()->parameters('month')
    }

}
