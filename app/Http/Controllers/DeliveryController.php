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

    public function getPlace_name()
    {
        $response = \GoogleMaps::load('geocoding')
                    ->setParamByKey('place_id', 'ChIJ_bmww9E0zDERB7Hzkrf7568') 
                    ->get();

        $area = json_decode($response, true);
        $collection = collect(json_decode($response)->results);
        $address_components = collect($collection->first()->address_components);
        $place_name = $address_components->where('types', ['locality', 'political']);

        dd($place_name);

        return $place_id;
    }

    public function getPontential_driver()
    {
        // the mines = ChIJTS54v7HKzTERb_UYK_CQXtA
        $collection_driver = collect();
        $drivers = DB::table('users')->where('current_postcode', 43200)->where('online_status', 0)->get();
        // dd($drivers);
        foreach($drivers as $driver)
        {
            if($this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid) <= '15000')
            {
                $collection_driver->push($driver);
            }

            if($collection_driver->isEmpty())
            {
                if($this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid) <= '30000')
                {
                    $collection_driver->push($driver);
                }
            }

            if($collection_driver->isEmpty())
            {
                if($this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid) <= '45000')
                {
                    $collection_driver->push($driver);
                }
            }
        }
        // $collection_driver = collect($drivers);
        // dd($collection_driver->pluck('id'));

        $collection = collect($collection_driver)->pluck('id');
        $this->sendPusher($collection->toArray(), 0);
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

            event(new \App\Events\DriverPusherEvent('62, Jalan persiaran, taman taming jaya.', $drivers[$index], $index, $drivers));
        }
        else
        {
            echo 'no more';
            // No more driver
            // Tell user no driver found
        }
    }

    public function driver_response(Request $request)
    {
        if( strcasecmp($request->acceptance, 'decline') == 0 )
        {
            $this->sendPusher($request->drivers, $request->index + 1);
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
