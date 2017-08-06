@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="/css/dropzone.css">
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="pull-right"><a class="btn btn-primary" href="/availability">Back</a></div>
            
            <div class="panel panel-default">
                <div class="panel-heading">Availability</div>
                <div class="panel-body">
                    <div class="form-group">                      
                        <label class="col-md-4">Date</label>
                        <label class="col-md-8" id="date" name="date" value="{{$availability->date}}">{{$availability->date}}</label>
                    </div>            

                    <div class="form-group">
                        <label class="col-md-4">StartTime</label>
                        <label class="col-md-8">{{$availability->start_time}}</label>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4">EndTime</label>
                        <label class="col-md-8">{{$availability->end_time}}</label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4">Day</label>
                        <label class="col-md-8">{{$availability->day}}</label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4">Available</label>
                        <label class="col-md-8">{{$availability->type}}</label>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3 col-md-offset-9">
                            <a href="#" id="check_id" onclick="checkDate()" class="btn btn-success">Edit Availability</a>
                        </div>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script type="text/javascript">
        function checkDate(){
            
            var dateInput = "{{$availability->date}}"; // For JQuery

            var myDate = new Date("{{$availability->date}}");

           
            var date = new Date();
            date.setDate(date.getDate() + 8);
           
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);

            var day_ahead = date.getFullYear()+"-"+(month)+"-"+(day) ;
            // 2017-6-30 > 2017-6-3
            // 2017-6-1 > 2017-6-3
            if (dateInput > day_ahead) {
                // alert("Entered date is greater than today's date ");
                $("#check_id").attr("href", "/availability/edit/{{$availability->id}}")            }
            else {
                alert("You can edit your availability after "+day_ahead);
            }
        }
    </script>
@endsection