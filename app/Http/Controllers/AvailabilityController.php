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

    public function index(){
        $auth = Auth::user();

        $availabilities = Availability::where('driver_id', $auth->id)->where('status', 'Alive')->get();
        
        return view('show.availability', ['availabilities' => $availabilities]);
    }

    public function editing($availability_id){
        $availability = Availability::where('id', $availability_id)->first();

        return view('edit.availabilityediting', ['availability' => $availability]);
    }

    public function edit($availability_id){

        $availability = Availability::where('id', $availability_id)->first();

        return view('edit.availability', ['availability' => $availability]);
    }

	public function create(Request $request){

		$auth = Auth::id();

        $type = $request->type ? "Activate" : "Not_Activate";

        //check the isset of each ID?
        $availability = Availability::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' =>$request->end_time,
            'type' => $type,
            'day' => $request->day,
            'driver_id' => $auth,
            'status' => "Alive",
            ]);

        return redirect()->action('AvailabilityController@index');
	}

    public function update(Request $request, $availability_id){

        $availability = Availability::where("id", $availability_id)->first();

        $type = $request->type ? "Activate" : "Not_Activate";

        $availability->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' =>$request->end_time,
            'type' => $type,
            'day' => $request->day,
            'status' => "Alive",
            ]);

        return redirect()->action('AvailabilityController@index');
    }

    public function delete($availability_id){
        $availability = Availability::where("id", $availability_id)->first();

        $availability->update([
            'status' => "Deleted",
            ]);

        return redirect()->back();
    }
}
