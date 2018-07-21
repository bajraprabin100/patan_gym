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
    Route::get('/bill-record', 'UserController@billRecord')->name('billRecord');
    Route::post('/bill-record-store', 'UserController@storeBillRecord')->name('billRecord.store');
    Route::get('/bill-record-list', 'UserController@listBillRecord')->name('billRecord.list');
    Route::get('/bill-record-list/{id}/edit', 'UserController@editBillRecord')->name('billRecord.edit');
    Route::post('/bill-record-list/update', 'UserController@updateBillRecord')->name('billRecord.update');
    Route::get('/bill-record-list/delete', 'UserController@deleteBillRecord')->name('billRecord.delete');

    Route::get('dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard']);
    Route::post('/branch_para/import', 'BranchparaController@import')->name('branch_para.import');
    Route::resource('/branch_para', 'BranchparaController');
    Route::post('/package/updateDetail', 'UserController@updatePackage')->name('package.update_detail');
    Route::get('/package/{id}/edit', 'UserController@editPackage');
    Route::delete('/package/{id}/deletePackage', 'UserController@deletePackage');
    Route::get('/package', 'UserController@package')->name('package');
    Route::post('/package', 'UserController@packageStore')->name('package.store');
    Route::get('/user/add', 'UserController@addUser')->name('user.add');
    Route::post('/user/store', 'UserController@storeUser')->name('user.store');
    Route::get('/user/package/selected', 'UserController@selectedPackage')->name('user.package.selected');
    Route::get('/user/list', 'UserController@userList')->name('user.list');
    Route::get('/user/bill_detail/{id}', 'UserController@billDetail')->name('user.bill_detail');
    Route::get('/user/{id}/edit', 'UserController@editUser')->name('user.edit');
    Route::post('/user/updateDetail', 'UserController@updateUser')->name('user.updateDetail');

    Route::group(['prefix' => 'utility', 'as' => 'utility.'], function () {
        Route::get('bill_issue', ['uses' => 'UtilityController@billIssueIndex', 'as' => 'bill_issue']);
        Route::post('bill_issue', ['uses' => 'UtilityController@billIssueStore', 'as' => 'bill_issue']);


    });


});
