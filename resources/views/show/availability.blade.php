@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Availabilities</div>
                <div class="panel-body">
                    @forelse($availabilities as $availability)
                    <div class="form-group">               
                        <label class="col-md-8">{{$availability->day}} > {{$availability->date}} > {{$availability->type}} </label>
                        <label class="col-md-2"> <a href="/availability/{{$availability->id}}" class="btn btn-success" >Details</a> </label>
                        <label class="col-md-2"><a href="/availability/delete/{{$availability->id}}" class="btn btn-danger">Delete</a></label>
                    </div>
                    @empty
                        <div class="form-group">
                            <label class="col-md-12">No Availabilities Found</label>
                            {{-- <label class="col-md-12">Click <a href="/availability/new">here</a> to add availability</label>  --}}
                        </div>
                    @endforelse
                    <div class="form-group">
                            <label class="col-md-12">Click <a href="/availability/new">here</a> to add availability</label> 
                    </div>                           
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


