@extends('layouts.app')

@section('title', 'Payment Cancelled')
@section('content')
  <section style="padding: 150px; 0px; 150px; 0px;">
    <div class="container">
      <div class="text-center mt-5">
        <h1>Payment Cancelled</h1>
        <p>Your payment has been cancelled. If you have any questions, please contact support.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
      </div>
    </div>
  </section>
@endsection