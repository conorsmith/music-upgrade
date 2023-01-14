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

Route::get('/', [
    'as'   => 'home',
    'uses' => 'HomeController@home',
]);

Route::get('albums', [
    'as'   => 'albums',
    'uses' => 'HomeController@albums',
]);

Route::get('artists', [
    'as'   => 'artists',
    'uses' => 'HomeController@artists',
]);

Route::group(['middleware' => "auth"], function () {
    Route::get('dashboard', "Admin\ShowDashboard@__invoke");
    Route::get('weeks-albums/new', "Admin\ShowNewWeeksAlbumsForm@__invoke");
    Route::get('admin/albums', "Admin\ListAlbums@__invoke");
    Route::get('admin/album/{id}', "Admin\ShowAlbumForm@__invoke");

    Route::post('weeks-albums', "Admin\CreateWeeksAlbums@__invoke");
    Route::put('album/{id}', "Admin\ModifyAlbum@__invoke");
    Route::post('album/{id}/rating', "Admin\CreateAlbumRating@__invoke");
    Route::post('update', "Admin\ImportFromGoogleSheets@__invoke");

    Route::post('auth/trigger', [
        'as'   => 'auth.trigger',
        'uses' => 'Auth\GoogleAuthController@trigger',
    ]);
});

Route::group(['middleware' => "guest"], function () {
    Route::get('auth/login', 'Auth\AuthController@showLoginForm')->name('login');
    Route::post('auth/login', 'Auth\AuthController@login');
});

Route::get('auth/logout', 'Auth\AuthController@logout');

Route::get('auth/callback', [
    'as'   => 'auth.callback',
    'uses' => 'Auth\GoogleAuthController@callback',
]);
