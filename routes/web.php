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

Route::get('conversation', 'InboxController@create');
Route::post('conversation', 'InboxController@store');
Route::get('conversation/{id}', 'InboxController@show');
Route::post('message/{id}', 'InboxController@addMessage');
Route::delete('/conversation/{id}', 'InboxController@destroy');
Auth::routes();

Route::get('/home', 'InboxController@index')->name('home');
