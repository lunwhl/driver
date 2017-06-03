@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                    <p id="demo"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
var x = document.getElementById("demo");
    $( document ).ready(function() {
        swal({
          title: 'Enable GPS',
          text: 'Please ensure you share location if the browser is asking for permission.',
          type: 'warning',
          confirmButtonColor: '#3085d6',
          confirmButtonText: 'OK'
        }).then(function () {
          if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
              swal(
                'Geolocation is not supported by this browser.',
                'Please use another browser and try again.',
                'error'
            )
          }
        })
    });
  function showPosition(position) {
      axios.post('/map/coordinate', {long: position.coords.longitude, lat: position.coords.latitude});
  }

  function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
          swal(
            'User denied the request for Geolocation.',
            'Please ensure that you have switched on your share location.',
            'error'
          )
            break;
        case error.POSITION_UNAVAILABLE:
          swal(
              'Location information is unavailable.',
              'Please ensure that you provide a available location information.',
              'error'
          )
            break;
        case error.TIMEOUT:
          swal(
              'The request to get user location timed out.',
              'Please try again.',
              'error'
          )
            break;
        case error.UNKNOWN_ERROR:
          swal(
              'An unknown error occurred.',
              'Please try again.',
              'error'
          )
            break;
    }
}
</script>
@endsection


