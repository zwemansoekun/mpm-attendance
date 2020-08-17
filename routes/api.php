<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/setting',  'SettingController@index');
Route::post('/setting/updateMoney',  'SettingController@update');
Route::post('/setting/updateAm',  'SettingController@updateAm');
Route::post('/setting/updatePm',  'SettingController@updatePm');

Route::get('/delayTimes',  'DelayTimeController@index');
Route::post('/delayTime/updateAm/{id}',  'DelayTimeController@saveAm');
Route::post('/delayTime/updatePm/{id}',  'DelayTimeController@savePm');
Route::post('/delayTime/store',  'DelayTimeController@store');
