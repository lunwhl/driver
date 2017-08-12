<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{

    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function addresses()
    {
        return $this->hasMany('App\Address');
    }

    public function destination()
    {
        return $this->hasMany('App\Address')->where('type', 'delivery')->first();
    }

    public function pickup()
    {
        return $this->hasMany('App\Address')->where('type', 'pickup')->first();
    }

    public function geoCoding($test)
    {
    	$response = \GoogleMaps::load('geocoding')
        ->setParam (['address' => $test])
        ->get();

        return $response;
    }
}
