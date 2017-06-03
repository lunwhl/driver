<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Photo; //Importing the relevant files
use App\Availability; //Importing the relevant files
use Auth;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // So here right, we have 2 different methods,
        // Index and create
        // both methods are not linked together
        // So if you define $myObj at create, index wont know what is myObj
        return view('photo',[

            'name' => $myObj
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userAvailabilities = Availability::where('driver_id', '=', Auth::user()->id)->get();  
        return view('photo', ['userAvailabilities' => $userAvailabilities]);
    }

    public function storeDriverAvailability(Request $data)
    {
        //dd($data->toArray());
        // Availability::create($data->toArray());

        // Get the current authenticated user
        // auth()->user()->availabilities()->create(['begin_time' => $data->begin_time,
        //     'end_time' => $data->end_time,
        //     'date' => $data->date,

        //     'status' => $data->status,])
        // Create an availability for that user
        return Availability::create([
            'begin_time' => $data->begin_time,
            'end_time' => $data->end_time,
            'date' => $data->date,
            'driver_id' => 1,
            'status' => $data->status,
            ]);
    }

    public function pickDriver ()
    {
        $selectedDrivers=[];
        
        for($i = 0; $i < count($customerDistance); ++$i) {
            // return $customerDistance[$i]['name'];

             // if($customerDistance[$i]['driverCurrentLocation'] <= 30){
             //    array_push($selectedDrivers, [$customerDistance[$i]['driverName']]);
             //    return($customerDistance[$i]['driverName']);
             // }
            return($customerDistance[$i]['driverName']);
            break;
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // If you need another view, you need to use another method
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
