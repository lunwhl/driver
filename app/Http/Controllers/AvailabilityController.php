<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Availability;

class AvailabilityController extends Controller
{
	public function show(){

		return view ('index.availability');
	}

	public function create(Request $request){

		$auth = Auth::id();

        $type = $request->type ? "available" : "not_available";

        //check the isset of each ID?
        $availability = Availability::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' =>$request->end_time,
            'type' =>$type,
            'day' => $request->day,
            'driver_id' => $auth,
            'status' => "activate",
            ]);

	}

    public function index(Request $request){ 
        Log::info($request);
        ////aaaaaaa
        $delivery_datetime = Carbon::parse($request->delivery_datetime);

        $availabilities = Availability::where('type',"available")
                        ->where('status','activate')
                        ->where('date', $delivery_datetime->format('o-m-d'))
                        ->orWhere('day', $delivery_datetime->format('l'))
                        ->where('start_time', '<=' , $delivery_datetime->format('h:i:s'))
                        ->where('end_time', '>=' , $delivery_datetime->format('h:i:s'))
                        ->get();

        $availabilities_id = $availabilities->pluck('chef_id');

        $postcode = $request->postcode;    
        $chefs = Chef::whereIn('id', $availabilities_id)->where('postcode',$postcode)->get();
        if($request->isHalal == true){            
            $users = User::where('is_halal','1')->get()->pluck('id');         
            $chefs = $chefs->whereIn('user_id',$users)->get();         
        }
        
        return $chefs->load('medias');
        //aaaaaaaa

    }
}
