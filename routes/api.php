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

// Route::get('/setting',  'SettingController@index');
// Route::post('/setting/updateMoney/{id}',  'SettingController@updateMoney');
// Route::post('/setting/updateAm/{id}',  'SettingController@updateAm');
// Route::post('/setting/updatePm/{id}',  'SettingController@updatePm');
// Route::get('/setting/all', 'SettingController@all');
Route::get('/setting/delayTime/{year}/{month}','SettingController@delayTime')->where(['year' => '[0-9]+', 'month' =>'[0-9]+']);


// Route::get('/delayTimes',  'DelayTimeController@index');
// Route::post('/delayTime/updateAm/{id}',  'DelayTimeController@saveAm');
// Route::post('/delayTime/updatePm/{id}',  'DelayTimeController@savePm');
Route::post('/delayTime/updateMoney/{id}',  'DelayTimeController@saveMoney');


Route::get('/holidays', 'HolidayController@index');
Route::post('/holidays', 'HolidayController@add');
Route::get('/holidays/copy', 'HolidayController@copy');
Route::get('/holidays/findYear/{year}', 'HolidayController@findYear');
Route::post('/holidays/deleteRow/{id}/{year}', 'HolidayController@deleteRow');

// Route::get('/employee/{emp_id}', 'EmployeeController@findByEmployee');
// Route::post('/employee/save/{id}', 'EmployeeController@save');

// Route::get('/employeeDetail/lastData/{emp_id}', 'EmployeeDetailController@findLastDataByEmployee');
// Route::get('/employeeDetail/{emp_id}', 'EmployeeDetailController@findByEmployee');
// Route::post('/employeeDetail/updateAll', 'EmployeeDetailController@updateAll');

Route::get('/attendManage', 'AttendManageController@index');
Route::post('/attendManage/csvOutput/{year}', 'AttendManageController@csvOutput');





