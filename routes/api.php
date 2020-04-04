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



Route::group(['namespace' => 'Api'], function () {
    Route::post('/line/callback', 'LineBotController@callback')->name('line.callback');
    Route::get('/line/callback', 'LineBotController@callback')->name('line.callback');

});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});




