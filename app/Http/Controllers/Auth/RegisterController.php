<?php

namespace App\Http\Controllers\Auth;

use App;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Intervention\Image\ImageManagerStatic as Image;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(Request $request)
    {
        return User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'identity' =>$request->identity,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'phone' => $request->phone,
            'state' => $request->state,
            'city' => $request->city,
            'nationality' => $request->nationality,
            'gender' => $request->gender,
            'bank' => $request->bank,
            'account' => $request->account,
            'role' => '0',
            'online_status' => '0',
            'status' => '0',
            'password' => bcrypt($request->password),
        ]);
    }

    public function register(Request $request)
    {
        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'identity' =>$request->identity,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'phone' => $request->phone,
            'state' => $request->state,
            'city' => $request->city,
            'nationality' => $request->nationality,
            'gender' => $request->gender,
            'bank' => $request->bank,
            'account' => $request->account,
            'role' => '0',
            'online_status' => 'offline',
            'status' => '0',
            'delivery_status' => 'finish',
            'number_plate' => $request->number_plate,
            'password' => bcrypt($request->password),
        ]);

        $User = User::all()->last();
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

        $IC = Input::file('ICimage');
        if( !empty( $IC ) ):

            $ext1 = $IC->getClientOriginalExtension();
            $filename1 = $User->id . "IC." . "JPG";
            //save IC into identity in user table
            $user = User::find($User->id);
            $user->ic = $filename1;
            $user->save();
            if( App::environment('local') )
            {
                // Storage::put('public/images/'.$filename1, file_get_contents($IC));
                $img = Image::make( $IC->getRealPath() );
                $path = 'images/' . $filename1;
                $this->SaveImage( $img, $path );
            }
            else if( App::environment('production') )
            {
                // $s3 = \Storage::disk('s3');
                // $s3->put($filePath, file_get_contents($IC), 'public');
                $img = Image::make( $IC->getRealPath() );
                    $path = 'images/' . $filename1;
                    $this->SaveImage( $img, $path );
            }


        endif;

        $License = Input::file('Licenseimage');
        if( !empty( $License ) ):

            $ext1 = $License->getClientOriginalExtension();
            $filename1 = $User->id . "License." . "JPG";
            //save IC into identity in user table
            $user = User::find($User->id);
            $user->license = $filename1;
            $user->save();
            if( App::environment('local') )
            {
                // Storage::put('public/images/'.$filename1, file_get_contents($IC));
                $img = Image::make( $License->getRealPath() );
                $path = 'images/' . $filename1;
                $this->SaveImage( $img, $path );
            }
            else if( App::environment('production') )
            {
                // $s3 = \Storage::disk('s3');
                // $s3->put($filePath, file_get_contents($IC), 'public');
                $img = Image::make( $License->getRealPath() );
                    $path = 'images/' . $filename1;
                    $this->SaveImage( $img, $path );
            }


        endif;

        $user->save();

      return redirect('/login');  
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

}