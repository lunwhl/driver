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
        return view('updateAvailability', ['userAvailabilities' => $userAvailabilities]);  
    }

    public function updateAvailabilityIntoDB(Request $data, $id)
    {
            // return Availability::post([
            // 'begin_time' => $data->begin_time,
            // 'end_time' => $data->end_time,
            // 'date' => $data->date,
            // 'driver_id' => 1,
            // 'status' => $data->status,
            // ]);
            // echo $id;
            $availability = Availability::find($id);
            $availability->update([
            'begin_time' => $data->begin_time,
            'end_time' => $data->end_time,
            'date' => $availability->date,
            'driver_id' => $availability->driver_id,
            'status' => $data->status,
            ]);

            return redirect()->back();
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
