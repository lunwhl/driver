<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.css">

<!-- Include a polyfill for ES6 Promises (optional) for IE11 and Android browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.common.js"></script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/home">Home</a></li>
                                    <li><a href="/profile">Profile</a></li>
                                    <li><a href="/delivery/index">Delivery History</a></li>
                                    <li><a href="/availability">Availability</a></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
                <form id="acceptance" method="post" action="/map/acceptance">
                  {!! csrf_field() !!}
                  <input style="display:none;" id="acceptance" type="text" name="acceptance">
                  <input style="display:none;" id="index" type="text" name="index">
                  <input style="display:none;" id="id" type="text" name="id">
                  <input style="display:none;" id="address" type="text" name="address">
                  <input style="display:none;" id="order_id" type="text" name="order_id">
                  <input style="display:none;" id="longitude" type="text" name="longitude">
                  <input style="display:none;" id="latitude" type="text" name="latitude">
                </form>
            </div>
        </nav>
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://js.pusher.com/3.1/pusher.min.js"></script>
    <script>
      //instantiate a Pusher object with our Credential's key
      var pusher = new Pusher('7e3f96ac11358fca61a6', {
          cluster: 'ap1',
          encrypted: false
      });

      //Subscribe to the channel we specified in our Laravel Event
      var channel = pusher.subscribe('channel-name-{{ auth()->id() }}');
      var deliveryCancel = pusher.subscribe('delivery-cancel-{{ auth()->id() }}');
      var pickupChannel = pusher.subscribe('channel-pickup-{{ auth()->id() }}');

      //Pickup Event
      pickupChannel.bind('App\\Events\\DriverPusherEvent', pickUpMessage);

      function pickUpMessage(data){
        swal(
            data.message,
            data.address,
            'success'
          )
      }

      //Event for delivery cancel
      deliveryCancel.bind('App\\Events\\DeliveryCancel', deliveryCancelMessage);

      function deliveryCancelMessage(data){
        swal(
            'Good job!',
            'You clicked the button!',
            'success'
          )
      }

      //Bind a function to a Event (the full Laravel class)
      channel.bind('App\\Events\\DriverPusherEvent', addMessage);

      function addMessage(data) {
        var
          closeInSeconds = 10,
          displayText = data.message + "\n" + "Please response in #1 seconds.",
          timer;
        swal({
          title: 'You Have a Delivery Request?',
          text: displayText.replace(/#1/, closeInSeconds),
          timer: closeInSeconds * 1000, 
          type: 'info',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Accept',
          cancelButtonText: 'Decline',
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger',
          buttonsStyling: false
        }).then(function () {
          swal(
            'Good Luck',
            'Delivery accepted.'
          )
          clearInterval(timer);
          $('#acceptance').val("accept");
          $('#index').val(data.index);
          $('#id').val(data.id);
          $('#address').val(data.message);
          $('#order_id').val(data.order_id);
          $('#latitude').val(data.userLat);
          $('#longitude').val(data.userLong);
          $('#acceptance').submit();
        }, function (dismiss) {
          // dismiss can be 'cancel', 'overlay',
          // 'close', and 'timer'
          clearInterval(timer);
          if (dismiss === 'cancel' || dismiss === 'timer') {
            axios.post('/map/acceptance', {acceptance: "decline", index: data.index, id: data.id, drivers: data.drivers, order_id: data.order_id, address: data.message, userLat: data.userLat, userLong: data.userLong});
          }
        })
        timer = setInterval(function() {
          closeInSeconds--;
            if (closeInSeconds < 0) {
                clearInterval(timer);
            }
          $('.swal2-content').text(displayText.replace(/#1/, closeInSeconds));
        }, 1000);
      }
    </script>
    @yield('js')
    <!-- <script>
      function idleLogout(){
        var t;
        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onmousedown = resetTimer;
        window.onclick = resetTimer;
        window.onscroll = resetTimer;
        window.onkeypress = resetTimer;

        function logout(){
          window.location.href = 'logout';
        }

        function resetTimer(){
          clearTimeout(t);
          t = setTimeout(logout, 60000);
        }
      }
    idleLogout();
    </script> -->

</body>
</html>
