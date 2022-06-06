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
    return redirect()->route('dashboard.index');
});

Auth::routes();

Route::match(['get', 'post'], '/bot', 'BotController@handle');
Route::get('/bot/tinker', 'BotController@tinker');


Route::middleware(['auth'])->group(function () {

    //Dashboard
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', 'DashboardController@index')->name('index');

        //Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::post('/self-update', 'ProfileController@selfUpdate')->name('self-update');
            Route::get('/', 'ProfileController@profile')->name('index');
        });
    });

    Route::middleware(['role:admin'])->group(function() {

        //Users
        Route::prefix('users')->name('users.')->group(function() {
            Route::get('import', 'UsersController@importView')->name('import-view');
            Route::post('import', 'UsersController@import')->name('import');
        });
        Route::resource('users', 'UsersController');
    });

    Route::middleware(['role:admin|manager'])->group(function() {

        //Orders
        Route::prefix('orders')->name('orders.')->group(function() {
            Route::get('/', 'OrdersController@index')->name('index');
            Route::get('export', 'OrdersController@export')->name('export');
            Route::get('send-message/{message}', 'OrdersController@sendMessage')->name('send-message');
        });

        //Menu
        Route::prefix('menu')->name('menu.')->group(function() {
            Route::post('upload-menu', 'MenuController@uploadMenu')->name('upload-menu');
        });
        Route::resource('menu', 'MenuController');

    });
});

