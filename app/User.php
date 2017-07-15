<?php

namespace App;

use App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use \HighIdeas\UsersOnline\Traits\UsersOnlineTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function deliveries()
    {
        return $this->hasMany('App\Delivery');
    }

    public function availabilities()
    {
        return $this->hasMany('App\Availability');
    }

    public static function urlPath()
    {
        $path = '/images';
        if(App::environment('local'))
        {
            $path = '/images';
            
        }elseif (App::environment('production')) {
            $path = 'https://s3-ap-southeast-1.amazonaws.com/photofactory-bucket/images';   
        }

        return $path;
    }

    public function getLatLngAttribute() {
        return $this->lat . ',' . $this->long;
    }
}
