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
    Route::get('notifications/view_all', 'UserController@viewNotifications')->name('notification.view_all');
    Route::get('notifications/query', 'UserController@queryNotifications')->name('notification.query');
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
    Route::get('/user/member/selected', 'UserController@memberSelected')->name('user.member.selected');
    Route::post('/user/store', 'UserController@storeUser')->name('user.store');
    Route::get('/user/package/selected', 'UserController@selectedPackage')->name('user.package.selected');
    Route::get('/user/list', 'UserController@userList')->name('user.list');
    Route::get('/user/bill_detail/{id}', 'UserController@billDetail')->name('user.bill_detail');
    Route::get('/user/{id}/edit', 'UserController@editUser')->name('user.edit');
    Route::post('/user/updateDetail', 'UserController@updateUser')->name('user.updateDetail');
    Route::get('/user/cash_entry', 'UserController@cashEntry')->name('user.cash_entry');
    Route::post('/user/cash_entry', 'UserController@cashEntryPost')->name('user.cash_entry.store');
    Route::get('/user/cash_entry/list', 'UserController@cashEntryList')->name('user.cash_entry.list');
    Route::get('/user/cash_entry/list/{id}/edit', 'UserController@editCashEntryList')->name('user.cash_entry.list.edit');
    Route::post('/user/cash_entry/list/update', 'UserController@updateCashEntryList')->name('user.cash_entry.list.update');
    Route::get('/user/cash_entry/list/delete', 'UserController@deleteCashEntryList')->name('user.cash_entry.list.delete');
    Route::post('/user/cash_entry/query', 'UserController@cashEntryQuery')->name('user.cash_entry.query');
    Route::get('/user/query', 'UserController@query')->name('user.query');
    Route::get('/user/bank_account', 'UserController@bankAccount')->name('user.bank_account');
    Route::post('/user/bank_account', 'UserController@bankEntryStore')->name('user.bank_account.store');
    Route::get('/user/bank_account/query', 'UserController@bankAccountQuery')->name('user.bank_account.queryList');
    Route::post('/user/bank_account/query', 'UserController@bankEntryQuery')->name('user.bank_account.query');
    Route::get('/user/valid_date','UserController@validDate')->name('user.valid_date');
    Route::group(['prefix' => 'utility', 'as' => 'utility.'], function () {
        Route::get('bill_issue', ['uses' => 'UtilityController@billIssueIndex', 'as' => 'bill_issue']);
        Route::post('bill_issue', ['uses' => 'UtilityController@billIssueStore', 'as' => 'bill_issue']);


    });


});
