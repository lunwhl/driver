<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function getGeocoding()
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

        dd(Deliver::geocoding("taming jaya"));
    }

    public function getGeoByCoordinate()
    {
        $response = \GoogleMaps::load('geocoding')
                ->setParamByKey('latlng', '3.068518,101.77042279999999') 
                 ->get();

        dd($response);
    }

    public function getDistance()
    {
    	$response = \GoogleMaps::load('directions')
        ->setParam (['origin' =>'place_id:ChIJ685WIFYViEgRHlHvBbiD5nE', 
                'destination' => 'place_id:ChIJA01I-8YVhkgRGJb0fW4UX7Y'])
        ->get();

        dd($response);
    }
}
