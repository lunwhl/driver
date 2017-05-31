<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo; //Importing the relevant files
use App\Availability; //Importing the relevant files

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
        //Ok, so I am guessing that you are using an empty object just for testing right?
        // For this kind of object you actually have to use Model but I guess that's ok for
        // Ok so now you get a different error haha
        // I think in php you cant call (object)
        // I am not sure about the syntax to create an anon object in PHP though. 
        // SO using an array should be enough for our test
        // I'll create an object instead of array now
        // User too much attributes...very mafan punya attribute ==
        // So lets create a model ourselves


        // $myObj = Photo::create([
        //         'name' => 'some name',
        //         'path' => 'some path',
        //     ]);

        // $driverAvailability = Availability::create([
        //     'begin_time' => '17:45',
        //     'end_time' => '19:45',
        //     'date' => '24/03/2012',
        //     'driver_id' => '1234',
        //     'status' => 'Available',
        //     ]);

        $flights = Photo::all();
        // $flights = Photo::where('name', 'some name');
        //$flights = Photo::find(1);

        $deliverRequest = [
            "chefLocation" => "Foo value",
            "customerLocation" => "Bar value",
            "food" => "nasiLemak"
        ];

        $driver= [
            'driverCurrentLocation' => '20',
            'driverName' => 'Ali',
        ];

        $driver2 = [
            'driverCurrentLocation' => '30',
            'driverName' => 'Muthu',
        ];

        $driver3 = [
            'driverCurrentLocation' => '40',
            'driverName' => 'Ah Hock',
        ];

        $listOfDrivers = [$driver, $driver2, $driver3];

        //So actually you need to call the view here instead of the Index
        // The other way, which we usually use at Controller, is dd(). It will format the things like array or object nicely so that it is easier for use to debug and it wouldn't load anything after that

        //dd($myObj);
        // So I think dd() is easier to look at when you have large arrays / objects
        // Wanna demonstrate that large object hahaha still thinking how to do.okok
        // Ok I saw we have some Models
        // So Models are actually Objects la

        //echo "Something to test"; // This line onwards shouldn't get executed
        // echo means print

        // $distance = self::distanceCalcuation($flights, $driver2);
        $distance = self::distanceCalcuation($flights, $driver2);

        // return view('photo', ['name' => $myObj]);

        return view('photo', ['name' => $distance]);
    }

    public function distanceCalcuation($customerDistance, $driverDistance)
    {
        // @for ($i = 0; $i < $listOfDrivers; $i++)
        
        // print($listOfDrivers[i]);
        // @endfor

        // $arr_length = count($customerDistance); 
        // $arr_length = count($driverDistance); 
        // for($i=0;$i<$arr_length;$i++) 
        // { 
        //     // calculations 
        //     // return print_r($customerDistance[$i]);
        //     return print_r($driverDistance);
        //     break;
        // }
        //unset($customerDistance[1]);
        // return print_r($customerDistance[1]);
        return $customerDistance;
    }

    protected function storeDriverAvailability(Request $data)
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
