@extends('layouts.app')  
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
            <div class="pull-right"><a class="btn btn-primary" href="/availability">Back</a></div>
                <div class="panel panel-default">
                    <div class="panel-heading">Availability</div>
                    <div class="panel-body">
                        <form id="av_form" class="form-horizontal" role="form" method="POST" action="/availability/add" enctype="multipart/form-data">
                            {{ csrf_field() }}
                                        {{-- <div class="form-group">
                                            <label class="col-md-offset-1 col-md-10"><font color="red">1. Add Media to correspond category</font></label>
                                        </div> --}}
                                        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}" id="date_input">
                                            <label class="col-md-offset-4 col-md-8"><font color="red">You can edit availability start 7 day advance</font></label>
                                            <label for="food_image" class="col-md-4 control-label">Date</label>

                                            <div class="col-md-6">
                                                <input id="date" type="date" class="form-control" name="date" value="{{ old('date') }}" required autofocus>

                                                @if ($errors->has('date'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('date') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('start_time') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">StartTime</label>

                                            <div class="col-md-6">
                                                <input id="startTime" type="time" class="form-control" name="start_time" value="{{ old('start_time') }}" required autofocus>
                                                @if ($errors->has('start_time'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('start_time') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('end_time') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">EndTime</label>

                                            <div class="col-md-6">
                                                <input id="endTime" type="time" class="form-control" name="end_time" value="{{ old('start_time') }}" required autofocus >
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">Day</label>

                                            <div class="col-md-6">
                                                <select class="form-control" name="day" required autofocus >
                                                
                                                    <option value="Monday">Monday</option>
                                                    <option value="Tuesday">Tuesday</option>
                                                    <option value="Wednesday">Wednesday</option>
                                                    <option value="Thursday">Thursday</option>
                                                    <option value="Friday">Friday</option>
                                                    <option value="Saturday">Saturday</option>
                                                    <option value="Sunday">Sunday</option>
                                                                       
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">Available</label>

                                            <div class="col-md-6">
                                                <input id="type" type="checkbox" class="form-control" name="type" checked >
                                            </div>
                                        </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <a onclick="checkDate()" class="btn btn-primary">
                                        Add
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@section('js')
<script type="text/javascript">
$( document ).ready(function() {

    //Current Date
    var date = new Date();
    date.setDate(date.getDate() + 8);

    // alert(dateMsg);
    var day = ("0" + (date.getDate())).slice(-2);
    var month = ("0" + (date.getMonth() + 1)).slice(-2);

    var today = date.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#date').val(today);


});

function checkDate() {
            var dateInput = $("#date").val(); // For JQuery

            var date = new Date();
            date.setDate(date.getDate() + 8);

            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);

            var day_ahead = date.getFullYear()+"-"+(month)+"-"+(day) ;
            if (dateInput >= day_ahead) {
                // alert("Entered date is greater than today's date ");

                $('#av_form').submit();
            }
            else {
                alert("You can only set your date later than " + day_ahead);
            }
        }

</script>
@endsection