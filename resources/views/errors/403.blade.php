@extends('layouts.app')

@section('title', 'Forbidden')
@section('content')
  <section style="padding: 150px; 0px; 150px; 0px;">
    <div class="container">
      <div class="text-center mt-5">
        <h1>403</h1>
        <p>You do not have permission to access this page.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
      </div>
    </div>
  </section>
@endsection