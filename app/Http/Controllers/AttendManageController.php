<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facade as Debugbar;
use App\Holiday;

class AttendManageController extends Controller
{
 
    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('attendManage');
    }

    /**
     * CSV出力する
     *
     */
    // public function csvOutput()
    // {
    //     Debugbar::info("csv");
    //     //return (new HolidaysExport)->download('invoices.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    //     //return (new HolidaysExport)->download('invoices.csv', Excel::CSV, ['Content-Type' => 'text/csv']);
    //     //(new HolidaysExport)->download('invoices.xlsx');
    //     //return Excel::download(new HolidaysExport, 'users.xlsx');
    //     Excel::store(new HolidaysExport(), 'invoices.xlsx');
    //     return (new HolidaysExport)->download('invoices.xlsx');
    //     //return "sucess";
    // }
}
