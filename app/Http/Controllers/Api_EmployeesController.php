<?php

namespace App\Http\Controllers;

use App\Api_Employees;
use Illuminate\Http\Request;

class Api_EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Api_Employees::all()->toArray();      
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Api_Employees  $api_Employees
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
     
       return Api_Employees::where('id',$id)->firstOrFail();    
    }

}
