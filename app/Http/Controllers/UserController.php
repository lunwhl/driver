<?php

namespace App\Http\Controllers;

use App;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Intervention\Image\ImageManagerStatic as Image;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
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
    public function show()
    {
        $auth = auth()->user();
        $path = User::urlPath();

        return view('show.profile', ['auth' => $auth, 'path' => $path]);
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
    public function update(Request $request)
    {
        $User = auth()->user();

        $User->update([
            'lname' => $request->lname,
            'fname' => $request->fname
            ]);

        $Car = Input::file('Carimage');
        if( !empty( $Car ) ):

            $ext1 = $Car->getClientOriginalExtension();
            $filename1 = $User->id . "car." . "JPG";
            //save IC into identity in user table
            $user = User::find($User->id);
            $user->license_plate = $filename1;
            $user->save();
            if( App::environment('local') )
            {
                // Storage::put('public/images/'.$filename1, file_get_contents($IC));
                $img = Image::make( $Car->getRealPath() );
                $path = 'images/' . $filename1;
                $this->SaveImage( $img, $path );
            }
            else if( App::environment('production') )
            {
                // $s3 = \Storage::disk('s3');
                // $s3->put($filePath, file_get_contents($IC), 'public');
                $img = Image::make( $Car->getRealPath() );
                    $path = 'images/' . $filename1;
                    $this->SaveImage( $img, $path );
            }
        endif;

        return redirect()->back();
    }

    public function SaveImage($image, $filename){
        if( App::environment('production') )
        {
            $image_target = $image->stream();

            $s3 = \Storage::disk('s3');
            $s3->put($filename, $image_target->__toString());
        }
        else
        {
            $image->save( $filename );
        }
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
