<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Availability;
use Auth;


class updateAvailability extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function showAvailability()
    {
        $userAvailabilities = Availability::where('driver_id', '=', Auth::user()->id)->get();  
        // dd($userAvailabilities);

        return view('updateAvailability', ['userAvailabilities' => $userAvailabilities]);  

    }

    public function updateAvailabilityIntoDB(Request $data)
    {
            return Availability::patch([
            'begin_time' => $data->begin_time,
            'end_time' => $data->end_time,
            'date' => $data->date,
            'driver_id' => 1,
            'status' => $data->status,
            ]);
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
