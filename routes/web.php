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

Route::get('/','IndexController@index');

Route::group(['middleware' => 'ftp_auth'], function () {
    Route::post('/get_rawlist','FtpController@get_rawlist');
    Route::post('/add_dir','FtpController@add_dir');
    Route::post('/remove','FtpController@remove');
    Route::post('/rename','FtpController@rename');
    Route::post('/create_file','FtpController@create_file');
    Route::get('/down_file','FtpController@down_file');
    Route::post('/upload_file','FtpController@upload_file');

});

