<?php

use Illuminate\Http\Request;

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
Route::group(['prefix'=>'/','middleware' => ['ability:admin,create-users']], function() {
    // Protected route
    Route::get('users', 'JwtAuthenticateController@index');
    Route::group(['prefix' => '/admin', 'namespace' => 'Admin'], function () {
        Route::post('admin/company_information', ['uses'=>'CompanyController@store','as'=>'api.admin.company_information.store']);
    });
});
// Route to create a new role
Route::post('role', 'JwtAuthenticateController@createRole');
// Route to create a new permission
Route::post('permission', 'JwtAuthenticateController@createPermission');
// Route to assign role to user
Route::post('assign-role', 'JwtAuthenticateController@assignRole');
// Route to attache permission to a role
Route::post('attach-permission', 'JwtAuthenticateController@attachPermission');
// Authentication route
Route::post('authenticate',['uses'=>'JwtAuthenticateController@authenticate','as'=>'app.authenticate']);
Route::post('login',['uses'=>'JwtAuthenticateController@postLogin']);
Route::group(['prefix'=>'/','middleware' => ['ability:destination|admin|local_admin,cors']], function() {
    Route::get('/manifest','Admin\ManifestController@apiManifestHomepage');
    Route::get('/manifest-detail/{manifest_no}','Admin\ManifestController@apiManifestDetail');
    Route::post('/track','Admin\ReportController@apiTrackBillNo');
    Route::get('/route_form_data','Admin\RouteDeliveryController@routeFormData');
});
