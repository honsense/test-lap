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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', 'AuthController@login');

Route::group(['middleware' => 'jwt.auth'], function(){
	Route::post('auth/register', 'AuthController@register');
	Route::get('getrequests', 'AuthController@getrequests');
	Route::get('getselectedrequest', 'AuthController@getselectedrequest');
	Route::get('getobservations', 'AuthController@getobservations');
	Route::post('changePass', 'AuthController@changePass');
	Route::post('passwordReset', 'AuthController@passwordReset');
	Route::post('test', 'TestController@test');
	Route::post('postObs', 'TestController@postObs');
	Route::post('approveObs', 'TestController@approveObs');
	Route::post('approveRequest', 'TestController@approveRequest');
	Route::get('auth/user', 'AuthController@user');
	Route::post('auth/logout', 'AuthController@logout');
});

Route::group(['middleware' => 'jwt.refresh'], function(){
	Route::get('auth/refresh', 'AuthController@refresh');
});
