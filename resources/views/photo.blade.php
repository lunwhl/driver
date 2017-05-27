@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <!-- Ok so now you get a different error cause you try to print the whole array out -->
                <div class="panel-body">
                <!-- For an array, you need to use print_r to show the whole array -->
                <!-- or dd() if you dont want the other things to load -->
                <!-- So print_r you can use here -->
                     <!-- <?= print_r($name);?> -->
                     Photo mei? <?= $name;?>  

                     @foreach ($name as $user)
                     </br>
                        This is user {{ $user->name}}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
