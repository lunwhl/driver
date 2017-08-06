@extends('layouts.app')  
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                <div class="pull-right"><a class="btn btn-primary" href="/availability">Back</a></div>
                    <div class="panel-heading">Edit Availability </div>
                    <div class="panel-body">
                        <form id="av_form" class="form-horizontal" role="form" method="POST" action="/availability/{{$availability->id}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                                        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}" id="date_input">
                                            <label for="food_image" class="col-md-4 control-label">Date</label>

                                            <div class="col-md-6">
                                                <input id="date" type="date" class="form-control" name="date" value="{{$availability->date}}">
                                            </div>
                                            @if ($errors->has('date'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('date') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">StartTime</label>

                                            <div class="col-md-6">
                                                <input id="startTime" type="time" class="form-control" name="start_time" value="{{$availability->start_time}}">
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">EndTime</label>

                                            <div class="col-md-6">
                                                <input id="endTime" type="time" class="form-control" name="end_time" value="{{$availability->end_time}}" >
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                            <label for="category" class="col-md-4 control-label">Day</label>

                                            <div class="col-md-6">
                                                <select class="form-control" name="day" id="day">
                                                
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
                                                @if($availability->type === "Activate")
                                                    <input id="type" type="checkbox" class="form-control" name="type" checked>
                                                @else
                                                    <input id="type" type="checkbox" class="form-control" name="type">
                                                @endif
                                            </div>
                                        </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <a class="btn btn-primary" onclick="checkDate()">
                                        Save
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
var $val = "{{$availability->type}}";
$( document ).ready(function() {

    //Assign value of day 
    document.getElementById('day').value = "{{$availability->day}}";
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