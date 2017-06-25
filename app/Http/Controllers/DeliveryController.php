<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Delivery;
use App\User;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::where('status', 'Finish')->where('driver_id', auth()->user()->id)->get();

        return view('index.delivery', ['deliveries' => $deliveries]);
    }

    public function show($delivery_id)
    {
        return view('show.delivery', ['delivery_id' => $delivery_id]);
    }

    public function updateFinish(Request $request)
    {
        $delivery = Delivery::find($request->id);

        $delivery->update([
            'status' => 'Finish'
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

    public function getDistance($origin, $destination)
    {
    	$response = \GoogleMaps::load('directions')
        ->setParam (['origin' =>'place_id:'.$origin, 
                'destination' => 'place_id:'.$destination])
        ->get();

        $collection = collect(json_decode($response)->routes);
        $collection_legs = collect($collection->first()->legs);
        $collection_distance = collect($collection_legs->first()->distance);
        $distance = $collection_distance['value'];
        
        return $distance;
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
        $lat = 3.06134909999999970000;
        $lng = 101.67525400000000000000;
        $address = "hehe";
        $order_id = 2;
        // user app need to provide place_id and address and postcode of the user to driver app.
        // the mines = ChIJTS54v7HKzTERb_UYK_CQXtA
        $User_Postcode = $this->getPostalCode($lat, $lng);
        $User_PlaceId = $this->getPlaceId($lat, $lng);

        $collection_driver = collect();
        $drivers = User::where('online_status', 'online')->get();
        // dd($drivers);
        foreach($drivers as $driver)
        {
            // echo $this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid);
            if($this->getDistance($User_PlaceId, $driver->current_placeid) <= '15000')
            {
                $collection_driver->push($driver);
            }
        }

        if($collection_driver->isEmpty())
        {
            foreach($drivers as $driver)
            {   
                if($this->getDistance($User_PlaceId, $driver->current_placeid) <= '30000')
                {
                    $collection_driver->push($driver);
                }
            }
        }

        if($collection_driver->isEmpty())
        {
            foreach($drivers as $driver)
            {
                if($this->getDistance($User_PlaceId, $driver->current_placeid) <= '45000')
                {
                    $collection_driver->push($driver);
                }
            }
        }           

        $collection = collect($collection_driver)->pluck('id');
        $this->sendPusher($collection->toArray(), 0, $address, $order_id);

        return response("Fuck Haw", 202);
    }

    // Listen for response, call the sender if no response or decline
    // Send the message to the driver
    public function sendPusher($drivers, $index, $address, $order_id)
    {
        if( $index != sizeOf($drivers) )
        {
            // Still have drivers to send
            // Get the next driver

            event(new \App\Events\DriverPusherEvent($address, $drivers[$index], $index, $drivers, $order_id));
        }
        else
        {
            echo 'no more';
            // No more driver
            //  maybe create a event to tell user no driver is found
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
            $delivery_id = Delivery::all()->last()->id;

            //  maybe create a event to tell user we found a driver
            $client = new Client();
            $request = $client->request('POST', 'http://dabao.welory.com.my/api/driver/result',
                                                    ['driver_name' => $driver->fname." ".$driver->lname,
                                                    'driver_id' => $driver->id,
                                                    'driver_image' => "testing image",
                                                    'status' => "found",
                                                    'order_id' => $request->order_id]);
                              //driver_name, driver_id, driver_image, status, order_id

            // return driver to delivery page
            return redirect()->action('DeliveryController@show', ['id' => $delivery_id]);
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
}
