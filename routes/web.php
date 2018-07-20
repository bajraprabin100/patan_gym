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
Route::get('/app/login', ['uses' => 'JwtAuthenticateController@appLogin', 'as' => 'app.login']);
Route::get('/app/logout', 'JwtAuthenticateController@logout')->name('app.logout');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['ability:admin|destination,create-users'], 'as' => 'admin.'], function () {
    // Protected route
    Route::get('dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard']);

    Route::post('/branch_para/import', 'BranchparaController@import')->name('branch_para.import');
    Route::resource('/branch_para', 'BranchparaController');
    Route::post('/branch_para/{id}/update_branch', 'BranchparaController@update_branch');
    Route::delete('/branch_para/{id}/destroy', 'BranchparaController@destroy');
    Route::group(['prefix' => 'utility', 'as' => 'utility.'], function () {

        Route::get('bill_issue', ['uses' => 'UtilityController@billIssueIndex', 'as' => 'bill_issue']);
        Route::post('bill_issue', ['uses' => 'UtilityController@billIssueStore', 'as' => 'bill_issue']);


    });






});
