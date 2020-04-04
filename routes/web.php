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

//管理者のみ
Route::group(['middleware' => 'auth'], function () {
	//管理者ダッシュボード
    Route::get('/admin/setup', 'Admin\ConfigController@setup')->name('setup');
    Route::post('/admin/setup', 'Admin\ConfigController@setup')->name('setup');

    Route::get('/admin/CreateSurvey', 'Admin\SurveyController@create')->name('CreateSurvey');
    Route::post('/admin/CreateSurvey', 'Admin\SurveyController@create')->name('CreateSurvey');

    Route::get('/admin/SummarySurvey', 'Admin\SurveyController@summary')->name('SummarySurvey');
    Route::get('/admin/inactive', 'Admin\SurveyController@inactive')->name('InactiveSurvey');
    Route::get('/admin/csv', 'Admin\SurveyController@download')->name('csv');
    Route::get('/admin/delete', 'Admin\SurveyController@delete')->name('delete');


});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
