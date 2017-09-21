<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $guarded = [];
    public function delivery()
    {
    	return $this->belongsTo('App\Delivery');
    }
}
