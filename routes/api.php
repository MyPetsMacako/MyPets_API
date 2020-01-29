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
Route::post('register', 'UserController@store');
Route::post('login', 'UserController@login');
Route::post('adminLogin', 'UserController@adminLogin');
Route::post('passrestore', 'UserController@passrestore');
Route::post('registerRole', 'RoleController@store');
Route::get('showUsersData', 'UserController@index');
