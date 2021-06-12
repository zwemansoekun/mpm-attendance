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

Auth::routes(['register' => false]);

// Route::get('/',  function () {
//         return view('layouts.app');
// })->middleware('auth');

Route::group(['middleware' => 'auth'], function()
{

Route::get('/',  function () {
      return view('layouts.app');
});

Route::get('/attendList/csvOutput/{employee}/{date}','AttendController@csvOutput')->where('date', '[0-9]+');
Route::post('/attendList','AttendController@store');
Route::post('/attendList/getmonth','AttendController@getmonth');
Route::get('/attendances/all/date', 'Api_AttendController@index');
Route::get('/attendances/ampm/{emp_no}/{date}', 'Api_AttendController@show')->where('emp_no', '[0-9]+')->where('date', '[0-9]+');

Route::post('/attendManage/csvOutput/{year}', 'AttendManageController@csvOutput');

Route::get('/salaryList/{yearmonth}', 'SalaryController@list')->where('yearmonth', '[0-9 -]+');
Route::get('/salaryList/ssb/all', 'SalaryController@ssb');
Route::get('/salary/export/{yearMonth}/{employees}', 'SalaryController@excel_export');
Route::get('/salaryList/download/{year}','SalaryController@download')->where('year', '[0-9 -]+');
Route::post('/salaryList', 'SalaryController@store');
Route::post('/salaryList/getsalary','SalaryController@getsalary');

Route::get('/dsettings','DefaultSettingController@index');
Route::get('/attendManage/download/{year}', 'AttendManageController@download');

Route::get('/settings',  'SettingController@index');
Route::get('/setting/all', 'SettingController@all');
Route::post('/setting/updateMoney/{id}',  'SettingController@updateMoney');
Route::post('/setting/updateAm/{id}',  'SettingController@updateAm');
Route::post('/setting/updatePm/{id}',  'SettingController@updatePm');
Route::get('/setting/delayTime/{year}/{month}','SettingController@delayTime')->where(['year' => '[0-9]+', 'month' =>'[0-9]+']);


Route::get('/delayTimes',  'DelayTimeController@index');
Route::post('/delayTime/updateAm/{id}',  'DelayTimeController@saveAm');
Route::post('/delayTime/updatePm/{id}',  'DelayTimeController@savePm');
Route::post('/delayTime/updateMoney/{id}',  'DelayTimeController@saveMoney');

Route::get('/employee/{emp_id}', 'EmployeeController@findByEmployee');
Route::post('/employee/save/{id}', 'EmployeeController@save');
Route::get('/employees', 'Api_EmployeesController@index');

Route::get('/employeeDetail/lastData/{emp_id}', 'EmployeeDetailController@findLastDataByEmployee');
Route::get('/employeeDetail/{emp_id}', 'EmployeeDetailController@findByEmployee');
Route::post('/employeeDetail/updateAll', 'EmployeeDetailController@updateAll');

Route::get('/holiday/{yearmonth}', 'HolidayController@holiday')->where('yearmonth', '[0-9 -]+');
Route::get('/holidays', 'HolidayController@index');
Route::post('/holidays', 'HolidayController@add');
Route::get('/holidays/findYear/{year}', 'HolidayController@findYear');
Route::get('/holidays/copy', 'HolidayController@copy');
Route::post('/holidays/deleteRow/{id}/{year}', 'HolidayController@deleteRow');

Route::get('{any}', function () {
        return view('layouts.app');
})->where('any', '.*');

});

// Route::get('{any}', function () {
//          return view('layouts.app');
// })->where('any', '.*')->middleware('auth');
