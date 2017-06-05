<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function geoCoding($test)
    {
    	$response = \GoogleMaps::load('geocoding')
        ->setParam (['address' => $test])
        ->get();

        return $response;
    }
}
