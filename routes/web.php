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
    event(new App\Events\DriverPusherEvent('Testing pusher', 1, 1, 1));
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
Route::post('/map/acceptance', 'DeliveryController@driver_response');
Route::get('/map/placename', 'DeliveryController@getPlace_name');

Route::get('/delivery/index', 'DeliveryController@index');
Route::get('/delivery/index/{id}', 'DeliveryController@show');
Route::post('/delivery/complete', 'DeliveryController@updateFinish');

Route::get('/profile', 'UserController@show');
Route::post('/profile/{id}', 'UserController@update');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/photo', 'PhotoController@create'); 
Route::post('/photo', 'PhotoController@storeDriverAvailability'); 

Route::get('/updateAvailability', 'updateAvailability@showAvailability'); 
Route::post('/updateAvailability/{id}', 'updateAvailability@updateAvailabilityIntoDB'); 

// Route::patch('/updateAvailability', 'updateAvailability@updateAvailabilityIntoDB'); 
// Route::bind('/updateAvailability', function ($id) {
//   $user = User::findOrFail($id);
//    return $user->save();
//   //return $user->fill(Input::all());
// });
// you can skip this if you use route::resource, otherwise define it:
//Route::patch('users/{users}', 'UsersController@update');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

