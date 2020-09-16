<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Holiday;
use Barryvdh\Debugbar\Facade as Debugbar;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{

    /**
     * 今年の休日を取得する
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $holidays = Holiday::whereYear('date',date("Y"))->get();
        return response()->json($holidays);
    }

    /**
     * 休日を追加する
     *
     */
    public function add(Request $request)
    {
        Debugbar::info($request->all());
        foreach($request->all() as $key)
        {
          if(!array_key_exists("id", $key))
            {
                $holiday = new Holiday([
                    'date' => $key['date'],
                    'description' => $key['description']
                    ]);
                $holiday->save();
            }else
            {
                 $holiday = Holiday::find($key['id']);
                if($holiday->date != $key['date'])
                { 
                    $holiday->date = $key['date'];
                }
                if($holiday->description != $key['description'])
                { 
                    $holiday->description = $key['description'];
                }
                
                $holiday->save();
            }
        }
    }

     /**
     * 休日をコピーする
     *
     */
    public function copy()
    {
        $holidays = Holiday::select('date','description')->whereYear('date',date("Y"))->get();
        return response()->json($holidays);
    }

    /**
     * 選択した年によって休日を取得する
     *
     */
    public function findYear($year)
    {
       $holidays = Holiday::select('id','date','description')->whereYear('date',$year)->get();
        return response()->json($holidays);
    }

    /**
     * 選択したテーブルの行を削除する
     *
     */
    public function deleteRow($id,$year)
    {
   
        $each_id = explode(',' , $id);
        foreach($each_id as $id) {
            DB::table('holidays')->where('id', '=', $id)->delete();
        }
        $holidays = Holiday::select('id','date','description')->whereYear('date',$year)->get();
        return response()->json($holidays);
    }

}


