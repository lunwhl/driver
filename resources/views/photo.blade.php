    @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

                <form action="/photo" method="POST">

                @foreach($userAvailabilities as $user)
                    @php($driver_id = "$user->driver_id")
                @endforeach

                    {{ csrf_field() }}
                    <label for="beginTime" class="col-md-4 control-label">Begin Time</label>
                    <div class="col-md-6">
                        <input id="beginTime" type="text" class="form-control" name="begin_time" value="{{ old('beginTime') }}" required autofocus>
                    </div>

                    <label for="endTime" class="col-md-4 control-label">End Time</label>
                    <div class="col-md-6">
                        <input id="endTime" type="text" class="form-control" name="end_time" value="{{ old('endTime') }}" required autofocus>
                    </div>

                    <label for="dateAvailable" class="col-md-4 control-label">Date</label>
                    <div class="col-md-6">
                        <input id="dateAvailable" type="date" class="form-control" name="date" value="{{ old('dateAvailable') }}" required autofocus>
                    </div>

                    <label for="driverID" class="col-md-4 control-label">Driver ID</label>
                    <div class="col-md-6">
                    <input id="driverID" type="text" disabled="YES" class="form-control" name="driver_id" value={{$driver_id}} required autofocus>
                    </div>


                    <label for="status" class="col-md-4 control-label">Status</label>
                    <div class="col-md-6">
                        
                        <select class="form-control" name="status" required >
                            <option selected value="available">Available</option>
                            <option value="not_available">Not available</option>
                        </select>
                    </div>

                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Register
                        </button>
                    </div>

                </form>


            <!-- </div> -->
        </div>
    </div>
</div>
@endsection
