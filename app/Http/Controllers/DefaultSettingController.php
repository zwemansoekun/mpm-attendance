<?php

namespace App\Http\Controllers;

use App\DefaultSetting;
use Illuminate\Http\Request;

class DefaultSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dsetting=DefaultSetting::all([
            'ssb_max','ssb_paid'
        ])->toArray();      
        if($dsetting){
            return $dsetting;
        }
        return '';
    }
   
}
