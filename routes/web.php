<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/dashboard', 'HomeController@index')->name('dashboard');
Route::resource('setting.name', 'CategoryController');
Route::get('/setting', 'CategoryController@indexx')->name('setting_name');
Route::resource('setting.list', 'CategoryListController');
// Route::get('/setting-list', 'CategoryListController@indexx')->name('setting_list');

Route::resource('entry.item', 'EntryController');

Route::get('/report/overview/{typeid}', 'ReportController@overview')->name('report_overview');
Route::get('/report/overview/{typeid}/last-entry', 'ReportController@getLastEntryOfaCategory');
Route::post('/report/overview/{typeid}/last-entry', 'ReportController@StoreMonthyExpenseEntry');
Route::get('/report/balance', 'ReportController@balance')->name('report_balance');
