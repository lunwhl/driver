<!DOCTYPE html>
<html>
  <head>
    <title>Talking with Pusher</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container">
      <div class="content">
        <h1>Laravel 5 and Pusher is fun!</h1>
        <ul id="messages" class="list-group">
        </ul>
      </div>
    </div>
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

      //Bind a function to a Event (the full Laravel class)
      channel.bind('App\\Events\\DriverPusherEvent', addMessage);

      function addMessage(data) {
        var listItem = $("<li class='list-group-item'></li>");
        listItem.html(data.message);
        $('#messages').prepend(listItem);
      }
    </script>
  </body>
</html>