<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', ['as' => 'index', 'uses' => 'Controller@index']);

Route::resource('data', 'DatasController');
Route::get('download_xml/{id}/{file}', ['as' => 'download_xml', 'uses' => 'DatasController@download_xml']);
Route::get('download_all/{id}', ['as' => 'download_all', 'uses' => 'DatasController@download_all']);
Route::get('search_data', ['as' => 'search_data', 'uses' => 'DatasController@search']);

Route::get('login', ['as' => 'login', 'uses' => 'LoginController@index']);
Route::post('checklogin', ['as' => 'checklogin', 'uses' => 'LoginController@checklogin']);
Route::get('successlogin', ['as' => 'successlogin', 'uses' => 'LoginController@successlogin']);
Route::get('logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);

Route::resource('user', 'UserController');
Route::get('search_user', ['as' => 'search_user', 'uses' => 'UserController@search']);