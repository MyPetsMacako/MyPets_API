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
Route::get('getQRData/{id}', 'PetController@getQRData');
//Route::get('showUsersData', 'UserController@index');

Route::middleware(['Checkout'])->group(function(){

    Route::get('showUsersData', 'UserController@index');
    Route::get('showUserData', 'UserController@showUserData');
    Route::delete('deleteUser/{id}', 'UserController@destroy');
    Route::post('ban/{id}', 'UserController@ban');
    Route::post('role/{id}', 'UserController@role');
    Route::post('restorePassword','UserController@restorePassword');
    Route::post('updateUser','UserController@update');
    Route::get('requestedUserInfo/{id}', 'UserController@adminRequestedUserInfo');
    Route::post('adminUpdate/{id}', 'UserController@updateForAdmin');
    Route::get('adminPanelInfo', 'UserController@adminPanelInfo');

    Route::post('petsRegister', 'PetController@store');
    Route::get('showPetsData', 'PetController@index');
    Route::delete('deletePet/{id}', 'PetController@destroy');
    Route::post('updatePet/{id}','PetController@update');
    Route::get('showPets', 'PetController@show');
    Route::get('adminRequestedPetInfo/{id}', 'PetController@adminRequestedPetInfo');
    Route::post('updatePetsForAdmin/{id}', 'PetController@updatePetsForAdmin');

    Route::get('showAppointmentsData', 'AppointmentController@index');
    Route::delete('deleteAppointment/{id}', 'AppointmentController@destroy');
    Route::post('createAppointment', 'AppointmentController@store');
    Route::post('updateAppointment/{id}', 'AppointmentController@update');
    Route::get('showAppointmentsByDateOrder', 'AppointmentController@showAppointmentsByDateOrder');
    Route::get('showAppointmentDetails/{id}', 'AppointmentController@showAppointmentDetails');

    //Route::post('adminPetsRegister', 'PetController@adminStore');
});

Route::group(['middleware' => 'cors'], function(){
    Route::get('showUsersData', 'UserController@index');
    Route::get('showUserData', 'UserController@showUserData');
    Route::delete('deleteUser/{id}', 'UserController@destroy');
    Route::post('ban/{id}', 'UserController@ban');
    Route::post('role/{id}', 'UserController@role');
    Route::post('restorePassword','UserController@restorePassword');
    Route::post('updateUser','UserController@update');
    Route::get('requestedUserInfo/{id}', 'UserController@adminRequestedUserInfo');
    Route::post('adminUpdate/{id}', 'UserController@updateForAdmin');
    Route::get('adminPanelInfo', 'UserController@adminPanelInfo');

    Route::post('petsRegister', 'PetController@store');
    Route::get('showPetsData', 'PetController@index');
    Route::delete('deletePet/{id}', 'PetController@destroy');
    Route::post('updatePet/{id}','PetController@update');
    Route::get('showPets', 'PetController@show');
    Route::get('adminRequestedPetInfo/{id}', 'PetController@adminRequestedPetInfo');
    Route::post('updatePetsForAdmin/{id}', 'PetController@updatePetsForAdmin');

    Route::get('showAppointmentsData', 'AppointmentController@index');
    Route::delete('deleteAppointment/{id}', 'AppointmentController@destroy');
    Route::post('createAppointment', 'AppointmentController@store');
    Route::post('updateAppointment/{id}', 'AppointmentController@update');
    Route::get('showAppointmentsByDateOrder', 'AppointmentController@showAppointmentsByDateOrder');
    Route::get('showAppointmentDetails/{id}', 'AppointmentController@showAppointmentDetails');
});
