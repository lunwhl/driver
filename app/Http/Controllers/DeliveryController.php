<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    public function getGeocoding(Request $request)
    {
    	// $response = \GoogleMaps::load('geocoding')
     //    ->setParam (['address' =>'taman taming jaya, balakong, selangor'])
     //    ->get();

     //    dd($response);

     //    // $area = json_decode($response, true);

     //    // foreach($area['results'] as $i => $v)
     //    // {
     //    // 	echo $v['geometry'].'<br/>';
     //    // }
     //    // dd(json_decode($response));
        // foreach(json_decode($response)->results as $area)
        // {
        // 	$lat = $area->geometry->location->lat;
        // 	$lng = $area->geometry->location->lng;
        // }
        // echo 'lat: ' . $lat . ' ' . 'lng: ' . $lng;

        // dd(Deliver::geocoding("taming jaya"));
        // return view('getgeolocation');
    }

    // public function getPostal_code()
    // {
    //     $response = \GoogleMaps::load('geocoding')
    //                 ->setParamByKey('latlng', '2.982703, 101.601289') 
    //                 ->get();

    //              $area = json_decode($response, true);
    //     // dd(json_decode($response)->results->first());
    //     $collection = collect(json_decode($response)->results);
    //     $address_components = collect($collection->first()->address_components);
    //     // dd($address_components->where('types', ['postal_code'])->first()->long_name);
    //     $postal_code = $address_components->where('types', ['postal_code'])->first()->long_name;
    //     echo $postal_code;
    //     // foreach(json_decode($response)->results as $area)
    //     // {
    //     //   $lat = $area->geometry->location->lat;
    //     //   $lng = $area->geometry->location->lng;
    //     // }

    //     // echo 'lat: ' . $lat . ' ' . 'lng: ' . $lng;
    // }

    public function getGeoByCoordinate()
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('latlng', '2.982703, 101.601289') 
                    ->get();

                 $area = json_decode($response, true);
        // dd(json_decode($response)->results->first());
        // $collection = collect(json_decode($response)->results);
        // $address_components = collect($collection->first()->address_components)
        // dd($address_components->where('types', ['postal_code'])->first()->long_name);
                 dd(json_decode($response)->results);
        foreach(json_decode($response)->results as $area)
        {
          $lat = $area->geometry->location->lat;
          $lng = $area->geometry->location->lng;
        }

        echo 'lat: ' . $lat . ' ' . 'lng: ' . $lng;

        // dd($response);
    }

    public function getDistance()
    {
    	$response = \GoogleMaps::load('directions')
        ->setParam (['origin' =>'place_id:ChIJ685WIFYViEgRHlHvBbiD5nE', 
                'destination' => 'place_id:ChIJA01I-8YVhkgRGJb0fW4UX7Y'])
        ->get();

        dd($response);
    }

    public function storeCoordinate(Request $request)
    {

        $postal_code = $this->getPostal_code($request->lat, $request->long);
        $place_id = $this->getPlace_id($request->lat, $request->long);

        $auth = auth()->user();

        $auth->update([
            'long' => $request->long,
            'lat' => $request->lat,
            'current_postcode' => $postal_code,
            'current_placeid' => $place_id
            ]);

        return response([], 200);
    }

    public function getPostal_code($lat, $long)
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

    public function getPlace_id($lat, $long)
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('latlng', $lat.','.$long) 
                    ->get();

        $area = json_decode($response, true);
        $collection = collect(json_decode($response)->results);
        $place_id = $collection->first()->place_id;

        return $place_id;
    }

    public function getPontential_driver()
    {
        $drivers = DB::table('users')->where('current_postcode', 43300)->get();

        $collection_driver = collect($drivers);
        dd($collection_driver->pluck('id'));

        // $collection = collect($drivers)->pluck('id');
        // $this->sendPusher($collection->toArray(), 0);
        //event(new \App\Events\DriverPusherEvent('in place id', 2));
        return "Event has been sent!";
    }

    // Listen for response, call the sender if no response or decline
    // Send the message to the driver
    public function sendPusher($drivers, $index)
    {
        if( $index != sizeOf($drivers) )
        {
            // Still have drivers to send
            // Get the next driver

            event(new \App\Events\DriverPusherEvent('in place id', $drivers[$index++]));
        }
        else
        {
            // No more driver
            // Tell user no driver found
        }
    }

    public function driver_response(Request $request)
    {

        if( !$request->acceptance )
        {
            // Driver denied
            $this->sendPusher($request->drivers, $request->index);
        }
        else
        {
            // Tell user we found a driver
            // $request->drivers[$request->index];
        }
    }


    // public function userAddress()
    // {
    //     $PontentialUsers = User::
    // }
}
