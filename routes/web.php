<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();//['register' => false]

Route::get('/',  function () {
        return view('layouts.app');
})->middleware('auth');

Route::get('/attendManage/download/{year}', 'AttendManageController@download');
Route::post('/attendList','AttendController@store');
Route::post('/attendList/getmonth','AttendController@getmonth');

Route::get('{any}', function () {
         return view('layouts.app');
})->where('any', '.*');


