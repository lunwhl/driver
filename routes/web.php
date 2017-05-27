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

Route::get('/pusher', function() {
    event(new App\Events\DriverPusherEvent('Hi there Pusher!', 2));
    return "Event has been sent!";
});

Route::get('/receiver', function(){
	return view('push');
});

Route::get('/map/geocoding', 'DeliveryController@getGeoByCoordinate');
Route::get('/map/distance', 'DeliveryController@getDistance');
Route::get('/map/geolocation','DeliveryController@getGeolocation');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/photo', 'PhotoController@create')->name('photo'); 
