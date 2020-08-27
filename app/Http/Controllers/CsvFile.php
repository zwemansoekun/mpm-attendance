<?php

namespace App\Http\Controllers;

use App\Exports\CsvExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CsvFile extends Controller
{
    public function csv_export(){
        // var_dump('aye');
        return Excel::download(new CsvExport, 'testing.csv');
    }
}
