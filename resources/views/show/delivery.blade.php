@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="col-md-6">
			<h2>Details</h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
	  <div class="col-md-6">
	  	<p>Pickup Time:<span>{{$delivery->pickup_time}}</span></p>
	  </div>
	  <div class="col-md-6">
	  	<p><span>Delivery Location:</span> <span>{{$delivery->delivery_location}}</span></p>
	  	<p><a class="btn btn-primary" href="#">Help</a></p>
	  </div>
	  @forelse($pickup_addresses as $key => $pickup_address)
		  <div class="col-md-6">
		  	<p><span>Pickup Location {{$key}}:</span> <span>{{$pickup_address->address_line}}</span></p>
		  	<p><a class="btn btn-primary" href="#">Help</a></p>
		  </div>
	  @empty
		  <div class="col-md-6">
		  	<p>No result</p>
		  </div>
	  @endforelse
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-6">
			<button onclick="finish()" class="btn btn-primary">Complete</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="col-md-6">
			<h2>Status</h2>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
	  <div class="col-md-10">
	  	<div class="col-md-2">
	  		<img id='demo-img' src="{{User::urlPath()}}/cooking-finish.png" style="width:auto; height:100px;" align="center"/>
	  		<p id="demo" style="margin-left:25px;"></p>
	  	</div>
	  	<div class="col-md-2">
	  		<img id='' src="{{User::urlPath()}}/arrow.png" style="width:auto; height:100px;" align="center"/>
	  	</div>
	  	<div class="col-md-2">
	  		<img id='demo-img-1' src="{{User::urlPath()}}/delivery.png" style="width:auto; height:100px;" align="center"/>
	  		<p id="demo-1" style="margin-left:25px;"></p>
	  	</div>
	  	<div class="col-md-2">
	  		<img src="{{User::urlPath()}}/arrow.png" style="width:auto; height:100px;" align="center"/>
	  	</div>
	  	<div class="col-md-2">
	  		<img id='demo-img-1' src="{{User::urlPath()}}/finish.png" style="width:auto; height:100px;" align="center"/>
	  		<p id="demo-2" style="margin-left:30px;"></p>
	  	</div>
	  </div>

	</div>
</div>
<input id="path" style="display:none;" value="{{User::urlPath()}}" />
<form id="post_id" method="post" action="/delivery/complete">
  {!! csrf_field() !!}
  <input style="display:none;" id="p_id" type="text" name="id">
  <input style="display:none;" id="u_id" type="text" name="user_id">
</form>
@endsection
@section('js')
	<script>
	$('#p_id').val("{{$delivery->id}}");
	$('#u_id').val("{{$user_id}}");
		function finish(){
			swal(
			  'Good job!',
			  '',
			  'success'
			)
			$('#post_id').submit();
		}
	</script>
	<script>
	var url = $('#path').val();
	// Set the date we're counting down to
	var countDownDate = new Date().getTime()+10000;

	// Update the count down every 1 second
	var x = setInterval(function() {

	    // Get todays date and time
	    var now = new Date().getTime();
	    
	    // Find the distance between now an the count down date
	    var distance = countDownDate - now;
	    
	    // Time calculations for days, hours, minutes and seconds
	    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	    
	    // Output the result in an element with id="demo"
	    document.getElementById("demo").innerHTML = minutes + "m " + seconds + "s ";
	    
	    // If the count down is over, write some text 
	    if (distance < 0) {
	        clearInterval(x);
	        $("#demo-img").attr("src",url+'/cooking.png');
	        document.getElementById("demo").innerHTML = "Finished";
	        var countDownDate1 = new Date().getTime()+10000;

			// Update the count down every 1 second
			var y = setInterval(function() {

			    // Get todays date and time
			    var now = new Date().getTime();
			    
			    // Find the distance between now an the count down date
			    var distance = countDownDate1 - now;
			    
			    // Time calculations for days, hours, minutes and seconds
			    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			    
			    // Output the result in an element with id="demo"
			    document.getElementById("demo-1").innerHTML = minutes + "m " + seconds + "s ";
			    
			    // If the count down is over, write some text 
			    if (distance < 0) {
			        clearInterval(y);
			        document.getElementById("demo-1").innerHTML = "Finished";
			        document.getElementById("demo-2").innerHTML = "Done";
			    }
			}, 1000);
	    }
	}, 1000);
</script>
@endsection