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
    event(new App\Events\DriverPusherEvent('Testing pusher', 2));
    return "Event has been sent!";
});

Route::get('/receiver', function(){
	return view('push');
});

Route::post('/map/coordinate', 'DeliveryController@storeCoordinate');
Route::get('/map/geocoding', 'DeliveryController@getGeoByCoordinate');
Route::get('/map/distance', 'DeliveryController@getDistance');
Route::get('/map/geolocation','DeliveryController@getGeocoding');
Route::get('/map/postal', 'DeliveryController@getPostal_code');
Route::get('/map/placeid', 'DeliveryController@getPlace_id');
Route::get('/map/driver', 'DeliveryController@getPontential_driver');

Route::post('/profile', 'UserController@update');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
