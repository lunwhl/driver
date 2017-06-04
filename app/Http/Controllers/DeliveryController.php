<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Delivery;
use App\User;

class DeliveryController extends Controller
{
    public function index()
    {
        $deliveries = Delivery::where('status', 'Finish')->where('driver_id', auth()->user()->id)->get();

        return view('index.delivery', ['deliveries' => $deliveries]);
    }

    public function show($id)
    {
        $delivery_id = Delivery::find($id)->id;
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

    public function getPlace_name($place_id)
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

    public function getPontential_driver()
    {
        // user app need to provide place_id and address and postcode of the user to driver app.
        // the mines = ChIJTS54v7HKzTERb_UYK_CQXtA
        $collection_driver = collect();
        // dd($drivers);
        foreach($drivers as $driver)
        {
            // echo $this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid);
            if($this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid) <= '15000')
            {
                $collection_driver->push($driver);
            }
        }

        if($collection_driver->isEmpty())
        {
            foreach($drivers as $driver)
            {   
                if($this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid) <= '30000')
                {
                    $collection_driver->push($driver);
                }
            }
        }

        if($collection_driver->isEmpty())
        {
            foreach($drivers as $driver)
            {
                if($this->getDistance('ChIJTS54v7HKzTERb_UYK_CQXtA', $driver->current_placeid) <= '45000')
                {
                    $collection_driver->push($driver);
                }
            }
        }           

        $collection = collect($collection_driver)->pluck('id');
        $this->sendPusher($collection->toArray(), 0);

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

            event(new \App\Events\DriverPusherEvent('62, Jalan persiaran, taman taming jaya. [user pass de address]', $drivers[$index], $index, $drivers));
        }
        else
        {
            echo 'no more';
            // No more driver
            //  maybe create a event to tell user no driver is found
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
            // Store delivery record
            $driver = User::find($request->id);
            Delivery::create([
                'delivery_location' => 'user app provide',
                'current_location' => $this->getPlace_name($driver->current_placeid),
                'amount' => '100',
                'order_id' => '1',
                'driver_id' => $request->id,
                'status' => 'Delivering'
                ]);
            $delivery_id = Delivery::all()->last()->id;

            //  maybe create a event to tell user we found a driver

            // return driver to delivery page
            // return redirect('/delivery/index');;
            return redirect()->action('DeliveryController@show', ['id' => $delivery_id]);

        }
    }


    // public function userAddress()
    // {
    //     $PontentialUsers = User::
    // }
}
