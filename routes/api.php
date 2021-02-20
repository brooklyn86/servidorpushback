<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => 'auth:api','namespace' => 'Api','prefix' => 'v1' ], function(){
    Route::get('details', 'UserController@details');
    Route::post('create/device', 'DeviceController@create');
    Route::post('devices', 'DeviceController@show');
    Route::get('users', 'UserController@users');
  });
  Route::group(['namespace' => 'Api','prefix' => 'v1'], function(){
      Route::post('login', 'UserController@login');
      Route::post('register', 'UserController@register');
      Route::post('upload', 'NotificationController@sendfileUpload');
      Route::post('create/notification/device', 'NotificationController@create');
      Route::post('create/token/client', 'NotificationController@createTokenClient');

  });
  