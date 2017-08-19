@extends('layouts.app')

@section('content')
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>Delivery Location</th>
					<th>Amount</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@forelse($deliveries as $delivery)
				<tr>
					<td>{{$delivery->delivery_location}}</td>
					<td>{{$delivery->amount}}</td>
					<td>{{$delivery->created_at}}</td>
				</tr>
				@empty
				No result to show.
				@endforelse
			</tbody>
		</table>
	</div>
@endsection