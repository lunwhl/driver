@extends('layouts.app')
@section('content')
<div class="container">
  <h2>Modal Example</h2>
  <!-- Trigger the modal with a button -->
  

  <!-- Modal -->
  @foreach($userAvailabilities as $user)
  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal-{{$user->id}}">Open Modal</button>
  <div class="modal fade" id="myModal-{{$user->id}}" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <p>Some text in the modal {{$user->id}}.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="#myModal-{{$user->id}}">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  @endforeach
  
</div>
@endsection