<?php

namespace App\Http\Controllers;

use App\Setting;
use App\DelayTime;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facade as Debugbar;
use Carbon\Carbon;

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

}
