<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Delivery;
use App\User;
use Carbon\Carbon;
use App\Availability;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::where('status', 'Finish')->where('driver_id', auth()->user()->id)->get();

        return view('index.delivery', ['deliveries' => $deliveries]);
    }

    public function show($delivery_id, $user_id)
    {
        return view('show.delivery', ['delivery_id' => $delivery_id, 'user_id' => $user_id]);
    }

    public function updateFinish(Request $request)
    {
        $delivery = Delivery::find($request->id);

        $delivery->update([
            'status' => 'Finish'
            ]);

        $driver = User::fina($request->user_id);

        $driver->update([
            'delivery_status' => 'Finish'
            ]);

        return redirect('/delivery/index');

    }

    public function getGeoByCoordinate()
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('latlng', '2.982703, 101.601289') 
                    ->get();

                 $area = json_decode($response, true);
                 dd(json_decode($response)->results);
        foreach(json_decode($response)->results as $area)
        {
          $lat = $area->geometry->location->lat;
          $lng = $area->geometry->location->lng;
        }

        echo 'lat: ' . $lat . ' ' . 'lng: ' . $lng;
    }

    public function getDistance($userCo, $driverCo)
    {
    	$response = \GoogleMaps::load('distancematrix')
        ->setParam (['origins' => $driverCo, 
                'destinations' => $userCo])
        ->get();

        $distance = json_decode($response, true);
        $collection = collect($distance);
        $row_collection = collect($collection['rows'])->flatten(2);
        $distance_collection = collect([]);
        
        foreach($row_collection as $key => $elements_collection)
        {
            $distance_collection->push([ "id" => $key, "distance" => $elements_collection['distance']['value']]);
        }
        $distance_collection->all();
        // dd($distance_collection);
        $filtered_collection = $distance_collection->filter(function ($item) {
            return $item["distance"] > 1;
        });
        // dd($filtered_collection);
        return $filtered_collection;

    }

    public function storeCoordinate(Request $request)
    {

        $postal_code = $this->getPostalCode($request->lat, $request->long);
        $place_id = $this->getPlaceId($request->lat, $request->long);

        $auth = auth()->user();

        $auth->update([
            'long' => $request->long,
            'lat' => $request->lat,
            'current_postcode' => $postal_code,
            'current_placeid' => $place_id
            ]);

        return response([], 200);
    }

    public function getPostalCode($lat, $long)
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('latlng', $lat.','.$long) 
                    ->get();
                
        $area = json_decode($response, true);
        $collection = collect(json_decode($response)->results);
        $address_components = collect($collection->first()->address_components);
        $postal_code = $address_components->where('types', ['postal_code'])->first()->long_name;

        return $postal_code;
    }

    public function getPlaceId($lat, $long)
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('latlng', $lat.','.$long) 
                    ->get();

        $area = json_decode($response, true);
        $collection = collect(json_decode($response)->results);
        $place_id = $collection->first()->place_id;

        return $place_id;
    }

    public function getPlaceName($place_id)
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('place_id', $place_id) 
                    ->get();

        $area = json_decode($response, true);
        $collection = collect(json_decode($response)->results);
        $address_components = collect($collection->first()->address_components);
        $place_name = $address_components->where('types', ['locality', 'political'])->first()->long_name;

        return $place_name;
    }

    public function getPotentialDriver(Request $request)
    {
        $user = new User;
        $users = $user->allOnline();

        $userLat = $request->latitude;
        $userLong = $request->longitude;
        $userCo = 3.06134909999999970000 . ',' . 101.67525400000000000000;
        $lng = 101.67525400000000000000;
        $address = "hehe";
        $order_id = $request->order_id;
        // dd($users);

        $delivery_datetime = Carbon::parse($request->delivery_datetime);

        // get users that are available
        $availabilities = Availability::where('type',"Activate")
                        ->where('status','Alive')
                        ->where('date', $delivery_datetime->format('o-m-d'))
                        ->orWhere('day', $delivery_datetime->format('l'))
                        ->where('start_time', '<=' , $delivery_datetime->format('h:i:s'))
                        ->where('end_time', '>=' , $delivery_datetime->format('h:i:s'))
                        ->get();

        // dd($availabilities);

        // collect all id from available list
        $availabilities_id = $availabilities->pluck('driver_id');
        // dd($availabilities_id);
        // dd($availabilities_id->isEmpty());
        if($availabilities_id->isNotEmpty()){
            // dd($availabilities_id);
            $availabilities_users = User::whereIn('id', $availabilities_id)->where('delivery_status', 'Finish')->get();
            // dd($availabilities_users);
            foreach($availabilities_users as $availabilities_user){
            $users->push($availabilities_user);
            }
        }
        // dd($users);
        // dd($availabilities_id);
        $users = $users->unique("id");

        // dd($users);
        // user app need to provide place_id and address and postcode of the user to driver app.
        // the mines = ChIJTS54v7HKzTERb_UYK_CQXtA
        // $User_Postcode = $this->getPostalCode($lat, $lng);
        // $User_PlaceId = $this->getPlaceId($lat, $lng);

        // pluck latt, long 
        $coordinates = $users->pluck('latLng')->implode('|');

        $driversWithinDistance = $this->getDistance($userCo, $coordinates);
        // dd($driversWithinDistance);
        // $potentialDrivers = $users->intersectKey($driversWithinDistance);   
        // dd($potentialDrivers);     
        $i = 0;
        foreach($users as $key => $user){
            $user->distance = $driversWithinDistance[$i]["distance"];
            $i++;
        }
        // dd($users->sortBy('distance'));
        $potentialDrivers = $users->sortBy('distance');

        $collection = collect($potentialDrivers)->pluck('id');

        $this->sendPusher($collection->toArray(), 0, $address, $order_id, $userLat, $userLong);

        return response("Fuck Haw", 202);
    }

    // Listen for response, call the sender if no response or decline
    // Send the message to the driver
    public function sendPusher($drivers, $index, $address, $order_id, $userLat, $userLong)
    {
        if( $index != sizeOf($drivers) )
        {
            // Still have drivers to send
            // Get the next driver
            $is_online_user = User::find($drivers[$index]);
            if($is_online_user->isOnline()){
                event(new \App\Events\DriverPusherEvent($address, $drivers[$index], $index, $drivers, $order_id, $userLat, $userLong));
            }else{
                $driver = User::find($drivers[$index]);
                $delivery = Delivery::create([
                    'delivery_location' => $address,
                    'current_location' => $this->getPlaceName($driver->current_placeid),
                    'amount' => '100',
                    'order_id' => $order_id,
                    'driver_id' => $drivers[$index],
                    'status' => 'Awaiting'
                    ]);

                $delivery->addresses()->create([
                    'type' => 'delivery',
                    'address_line' => $address,
                    'latitude' => $userLat,
                    'longitude' => $userLong
                    ]);
                $delivery_id = Delivery::all()->last()->id;

                //  maybe create a event to tell user we found a driver
                $client = new Client();
                $request = $client->request('POST', 'http://dabao.welory.com.my/api/driver/result', [
                                                        'form_params' => [
                                                            'driver_name' => $driver->fname." ".$driver->lname,
                                                            'driver_id' => $driver->id,
                                                            'driver_image' => "testing image",
                                                            'status' => "found",
                                                            'order_id' => $request->order_id
                                                            ]
                                                        ]);
            }
        }
        else
        {
            echo 'no more';
            $client = new Client();
            $request = $client->request('POST', 'http://dabao.welory.com.my/api/driver/result', [
                                                    'form_params' => [
                                                        'status' => "not found",
                                                        'order_id' => $request->order_id
                                                        ]
                                                    ]);
        }
    }

    public function getDriverResponse(Request $request)
    {
        if( strcasecmp($request->acceptance, 'decline') == 0 )
        {
            $this->sendPusher($request->drivers, $request->index + 1, $request->address, $request->order_id);
        }
        else
        {
            // Store delivery record
            $driver = User::find($request->id);
            Delivery::create([
                'delivery_location' => $request->address,
                'current_location' => $this->getPlaceName($driver->current_placeid),
                'amount' => '100',
                'order_id' => $request->order_id,
                'driver_id' => $request->id,
                'status' => 'Awaiting'
                ]);

            $delivery->addresses()->create([
                    'type' => 'delivery',
                    'address_line' => $request->address,
                    'latitude' => $request->userLat,
                    'longitude' => $request->userLong
                    ]);
            $delivery_id = Delivery::all()->last()->id;

            //  maybe create a event to tell user we found a driver
            $client = new Client();
            $request = $client->request('POST', 'http://dabao.welory.com.my/api/driver/result', [
                                                    'form_params' => [
                                                        'driver_name' => $driver->fname." ".$driver->lname,
                                                        'driver_id' => $driver->id,
                                                        'driver_image' => "testing image",
                                                        'status' => "found",
                                                        'order_id' => $request->order_id
                                                        ]
                                                    ]);
                              //driver_name, driver_id, driver_image, status, order_id

            // return driver to delivery page
            return redirect()->action('DeliveryController@show', ['id' => $delivery_id, 'user_id' => $driver->id]);
            // echo $request->order_id;

        }
    }

    public function getCancelResponse(Request $request)
    {
        $delivery = Delivery::find($request->delivery_id);

        $delivery::update([
            'status' => 'Canceled'
            ]);

        // create event to tell user delivery canceled
        event(new \App\Events\DeliveryCancel("Order has been canceled.", $request->delivery_id));
    }

    public function getPickupDetails(Request $request){
        $delivery = Delivery::where('order_id', $request->order_id)->last();

        $delivery->addresses()->create([
                'type' => 'pickup',
                'address_line' => $request->pickup_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

        return $delivery->user;
    }
}
