@extends('layouts.app')

@section('title', 'Page Not Found')
@section('content')
  <section style="padding: 150px; 0px; 150px; 0px;">
    <div class="container">
      <div class="text-center mt-5">
        <h1>404</h1>
        <p>Sorry, the page you are looking for could not be found.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
      </div>
    </div>
  </section>
@endsection