@extends('layouts.app')
@section('content')
<div class="container">
  <h2>Modal Example</h2>
  <!-- Trigger the modal with a button -->
  

  <!-- Modal -->
  @foreach($userAvailabilities as $user)
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal-{{$user->id}}">{{$user->date}}</button>

  	<!-- Declare variable at blade.php -->
	@php($old_section = "$user->begin_time")

  <div class="modal fade" id="myModal-{{$user->id}}" role="dialog">
  <div class="modal-dialog">
    
      <!-- Modal content-->
      <form action="/updateAvailability" method="PATCH">
		{{ csrf_field() }}

	      <div class="modal-content">
	        <div class="modal-header">
	          <button type="button" class="close" data-dismiss="modal">&times;</button>
	          <h4 class="modal-title">Editing date: {{$user->date}}</h4>
	        </div>
	        <div class="modal-body">
	          

			<p>
				<label for="beginTime" class="col-md-4 control-label">Begin Time</label>
				    <input id="beginTime" type="text" class="form-control" name="begin_time" value={{$user->begin_time}} required autofocus>
			</p>

			<p>
				<label for="endTime" class="col-md-4 control-label">End Time</label>
				    <input id="beginTime" type="text" class="form-control" name="end_time" value={{$user->end_time}} required autofocus>
			</p>

			<p>
				<label for="date" class="col-md-4 control-label">Date</label>
				    <input id="beginTime" type="date" class="form-control" name="date" value={{$user->date}} required autofocus>
			</p>

			<p>
				<label for="status" class="col-md-4 control-label">Status</label>
	                <select class="form-control" name="status" value="{{$user->status}}" required >
	                    <option value="available">Available</option>
	                    <option value="not_available">Not available</option>
	                </select>
			</p>


	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
	        <button type="submit" class="btn btn-primary">Register</button>
	      </div>
      </form>
    </div>
  </div>
  @endforeach
  
</div>
@endsection