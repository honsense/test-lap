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
	//requests
	Route::get('requests', 'RequestController@index');
	Route::get('requests/{id}', 'RequestController@show');
	Route::get('requests/{id}/observations', 'RequestController@observations');
	Route::post('requests/{id}/update', 'RequestController@update');
	Route::post('requests/create', 'RequestController@store');
	//observations
	Route::get('observations', 'ObservationController@index');
	Route::get('observations/{id}', 'ObservationController@show');
	Route::post('observations/{id}/update', 'ObservationController@update');
	Route::post('observations/create', 'ObservationController@store');
	Route::post('observations/approve', 'ObservationController@approve');
	//admin
	Route::post('changePass', 'AuthController@changePass');
	Route::post('passwordReset', 'AuthController@passwordReset');

	Route::get('auth/user', 'AuthController@user');
	Route::post('auth/logout', 'AuthController@logout');
});

Route::group(['middleware' => 'jwt.refresh'], function(){
	Route::get('auth/refresh', 'AuthController@refresh');
});