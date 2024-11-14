<?php

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', 'HomeController@index')->name('dashboard');
    Route::post('/dashboard', 'HomeController@index_data');

    Route::resource('setting.name', 'CategoryController');
    Route::get('/setting', 'CategoryController@indexx')->name('setting_name');
    Route::resource('setting.list', 'CategoryListController');
    Route::get('/setting-list', 'CategoryListController@indexx')->name('setting_list');
    
    Route::get('entry-header/index', 'EntryHeaderController@index')->name('entry-header.index');
    Route::post('entry-header/index', 'EntryHeaderController@updateDate');
    Route::get('entry-header/{id}/edit', 'EntryHeaderController@edit')->name('entry-header.edit');
    Route::get('entry-header/create', 'EntryHeaderController@create')->name('entry-header.create');
    Route::get('entry-header/pre-select', 'EntryHeaderController@select')->name('entry-header.selection');
    Route::get('entry-header/{id}/delete', 'EntryHeaderController@delete')->name('entry-header.delete');
    Route::delete('entry-header/index/{id}', 'EntryHeaderController@deleteEntry');
    Route::get('entry-header/{id}/complete', 'EntryHeaderController@complete')->name('entry-header.done');
    
    Route::get('entry/{id}/index', 'EntryController@index')->name('entry.item.index');
    Route::get('entry/{id}/index2', 'EntryController@index2')->name('entry.item.index2');
    Route::POST('entry/{id}/index', 'EntryController@store')->name('entry.item.store');
    Route::get('entry/{id}/index/create', 'EntryController@create')->name('entry.item.create'); //passing 0 as default
    Route::post('entry/{id}/index/preview', 'EntryController@preview');
    Route::get('entry/{id}/index/next', 'EntryController@next')->name('next');
    Route::get('entry/{id}/index/sendemail', 'EntryController@sendemail')->name('sendemail');
    Route::get('entry/{id}/index/next2', 'EntryController@next2');

    // Route::get('/report/overview/{typeid}', 'ReportController@test')->name('report_overview');
    // Route::get('/report/overview/{typeid}/last-entry', 'ReportController@getLastEntryOfaCategory');
    // Route::post('/report/overview/{typeid}/last-entry', 'ReportController@StoreMonthyExpenseEntry');
    // Route::get('/report/balance', 'ReportController@balance')->name('report_balance');
});
