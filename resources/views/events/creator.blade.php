@extends('layouts.normal-page')

@section('content')
    <div class="container">
        <events-creator @if(isset($event)) :event="{{ json_encode($event) }}" :guests="{{ json_encode($guests) }}" @endif></events-creator>
    </div>
@endsection

