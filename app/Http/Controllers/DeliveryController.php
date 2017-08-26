<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Delivery;
use App\User;
use Carbon\Carbon;
use App\Availability;

class DeliveryController extends Controller
{
    public function index()
    {
        Log::info("DeliveryController: index");
        $deliveries = Delivery::where('status', 'Finish')->where('user_id', auth()->user()->id)->get();

        return view('index.delivery', ['deliveries' => $deliveries]);
    }

    public function show($delivery_id)
    {
        Log::info("DeliveryController: show");
        $auth = auth()->id();

        return view('show.delivery', ['delivery_id' => $delivery_id, 'user_id' => $auth]);
    }

    public function updateFinish(Request $request)
    {
        Log::info("DeliveryController: updateFinish");
        $delivery = Delivery::find($request->id);

        $delivery->update([
            'status' => 'Finish'
            ]);

        $driver = User::find($request->user_id);

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
        Log::info("getDistance");
        $response = \GoogleMaps::load('distancematrix')
        ->setParam (['origins' => $driverCo, 
                'destinations' => $userCo])
        ->get();

        $distance = json_decode($response, true);
        $collection = collect($distance);
        $row_collection = collect($collection['rows'])->flatten(2);
        $distance_collection = collect([]);
        // dd($row_collection);
        foreach($row_collection as $key => $elements_collection)
        {
            $distance_collection->push([ "id" => $key, "distance" => $elements_collection['distance']['value']]);
        }
        $distance_collection->all();
        // dd($distance_collection);
        $filtered_collection = $distance_collection->filter(function ($item) {
            return $item["distance"] < 1001;
        });
        // dd($filtered_collection);
        return $filtered_collection;

    }

    public function localDistance()
    {
        $response = \GoogleMaps::load('distancematrix')
        ->setParam (['origins' => "3.028976,101.718337", 
                'destinations' => "3.032132 101.717088"])
        ->get();

        $distance = json_decode($response, true);
        $collection = collect($distance);
        $row_collection = collect($collection['rows'])->flatten(2);
        $distance_collection = collect([]);
        // dd($row_collection);
        foreach($row_collection as $key => $elements_collection)
        {
            $distance_collection->push([ "id" => $key, "distance" => $elements_collection['distance']['value']]);
        }
        $distance_collection->all();
        dd($distance_collection);
        $filtered_collection = $distance_collection->filter(function ($item) {
            return $item["distance"] < 1001;
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

    public function getPlaceName($driverCo)
    {        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('latlng', $driverCo)
                    ->get();

        $area = json_decode($response, true);
        // dd($area);
        $collection = collect(json_decode($response)->results);
        // dd($collection);
        $address_components = collect($collection->first()->address_components);
        $place_name = $address_components->where('types', ['locality', 'political'])->first()->long_name;
        // dd($place_name);
        return $place_name;
    }

    public function getPotentialDriver(Request $request)
    {
        Log::info("getPotentialDriver");
        $now = Carbon::now();
        $delivery_datetime = Carbon::parse($request->time);
        if($now->gt($delivery_datetime))
        {
            $user = new User;
            $users = $user->allOnline();
        }else{
            $users = collect();
        }

        $userLat = $request->latitude;
        $userLong = $request->longitude;
        $userCo = $userLat . ',' . $userLong;
        $address = $request->address;
        $order_id = $request->order_id;

        Log::info("getPotentialDriver: Availabilities");
        // get users that are available
        $availabilities = Availability::where('type',"Activate")
                        ->where('status','Alive')
                        ->where('date', $delivery_datetime->format('o-m-d'))
                        ->orWhere('day', $delivery_datetime->format('l'))
                        ->where('start_time', '<=' , $delivery_datetime->format('h:i:s'))
                        ->where('end_time', '>=' , $delivery_datetime->format('h:i:s'))
                        ->get();

        // collect all id from available list
        $availabilities_id = $availabilities->pluck('driver_id');
        if($availabilities_id->isNotEmpty()){
            $availabilities_users = User::whereIn('id', $availabilities_id)->where('delivery_status', 'Finish')->get();
            foreach($availabilities_users as $availabilities_user){
            $users->push($availabilities_user);
            }
        }

        // pluck latt, long 
        $coordinates = $users->pluck('latLng')->implode('|');

        Log::info("getPotentialDriver: driversWithinDistance");
        $driversWithinDistance = $this->getDistance($userCo, $coordinates);
        if($driversWithinDistance->isEmpty()){
            $this->sendPusher(array(), 0, $address, $order_id, $userLat, $userLong);
        }  
        
        $potentialDrivers = collect();
        foreach($driversWithinDistance as $key => $driverWithinDistance){
            $users[$key]->distance = $driverWithinDistance["distance"];
            $potentialDrivers->push($users[$key]);
        }

        // the potential drivers are here and sort by distance from shortest to furthest
        $potentialDrivers = $potentialDrivers->sortBy('distance');

        $collection = collect($potentialDrivers)->pluck('id');

        $this->sendPusher($collection->toArray(), 0, $address, $order_id, $userLat, $userLong);

        return response("Return message", 202);
    }

    // Listen for response, call the sender if no response or decline
    // Send the message to the driver
    public function sendPusher($drivers, $index, $address, $order_id, $userLat, $userLong)
    {
        Log::info("sendPusher");
        if( $index != sizeOf($drivers) )
        {
            // Still have drivers to send
            // Get the next driver
            $is_online_user = User::find($drivers[$index]);
            if($is_online_user->isOnline()){
                event(new \App\Events\DriverPusherEvent($address, $drivers[$index], $index, $drivers, $order_id, $userLat, $userLong));
            }else{
                // when there is no online user and only user that found from availability
                $driver = User::find($drivers[$index]);
                $delivery = Delivery::create([
                    'delivery_location' => $address,
                    'current_location' => $this->getPlaceName($driver->lat . "," . $driver->long),
                    'amount' => '100',
                    'order_id' => $order_id,
                    'user_id' => $drivers[$index],
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
                $client->request('POST', 'http://dabao.welory.com.my/api/driver/result', [
                                                        'form_params' => [
                                                            'driver_name' => $driver->fname." ".$driver->lname,
                                                            'driver_id' => $driver->id,
                                                            'driver_image' => "testing image",
                                                            'status' => "found",
                                                            'order_id' => $order_id
                                                            ]
                                                        ]);
            }
        }
        else
        {
            Log::info("sendPusher: no driver");
            // when there is no drivers found
            $client = new Client();
            $client->request('POST', 'http://dabao.welory.com.my/api/driver/result', [
                                                    'form_params' => [
                                                        'status' => "not found",
                                                        'order_id' => $order_id
                                                        ]
                                                    ]);
        }
    }

    public function getDriverResponse(Request $request)
    {
        Log::info("getDriverResponse");
        if( strcasecmp($request->acceptance, 'decline') == 0 )
        {
            Log::info("getDriverResponse: decline");
            $this->sendPusher($request->drivers, $request->index + 1, $request->address, $request->order_id,$request->userLat, $request->userLong);
        }
        else
        {
            Log::info("getDriverResponse: accepted");
            // Store delivery record
            $driver = User::find($request->id);
            $delivery = Delivery::create([
                'delivery_location' => $request->address,
                'current_location' => $this->getPlaceName($driver->lat . "," . $driver->long),
                'amount' => '100',
                'order_id' => $request->order_id,
                'user_id' => $request->id,
                'status' => 'Awaiting'
                ]);

            $delivery->addresses()->create([
                    'type' => 'delivery',
                    'address_line' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                    ]);
            $delivery_id = Delivery::all()->last()->id;

            //  maybe create a event to tell user we found a driver
            $client = new Client();
            $client->request('POST', 'http://dabao.welory.com.my/api/driver/result', [
                                                    'form_params' => [
                                                        'driver_name' => $driver->fname." ".$driver->lname,
                                                        'driver_id' => $driver->id,
                                                        'driver_image' => "testing image",
                                                        'status' => "found",
                                                        'order_id' => $request->order_id
                                                        ]
                                                    ]);
            return redirect()->back();
        }
    }

    public function getCancelResponse(Request $request)
    {
        Log::info("getCancelResponse");
        $delivery = Delivery::find($request->delivery_id);

        $delivery::update([
            'status' => 'Canceled'
            ]);

        // create event to tell user delivery canceled
        event(new \App\Events\DeliveryCancel("Order has been canceled.", $request->delivery_id));
    }

    public function getPickupDetails(Request $request){
        Log::info("getPickupDetails");
        $delivery = Delivery::where('order_id', $request->order_id)->first();

        $delivery->addresses()->create([
                'type' => 'pickup',
                'address_line' => $request->pickup_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

        event(new \App\Events\PickupEvent("Order Accepted.", $request->pickup_address, $delivery->id, $delivery->user_id));

        return $delivery->user;
    }
}
