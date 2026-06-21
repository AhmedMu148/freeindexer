@extends('layouts.app')

@section('title', 'Server Error')
@section('content')
  <section style="padding: 150px; 0px; 150px; 0px;">
    <div class="container">
      <div class="text-center mt-5">
        <h1>500</h1>
        <p>Whoops, something went wrong on our servers.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
      </div>
    </div>
  </section>
@endsection