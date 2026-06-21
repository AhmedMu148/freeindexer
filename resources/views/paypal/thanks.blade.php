@extends('layouts.app')

@section('title', 'Payment Successful')
@section('content')
  <section style="padding: 150px; 0px; 150px; 0px;">
    <div class="container">
      <div class="text-center mt-5">
        <h1>Payment Successful</h1>
        <p>Thank you for your payment. Your transaction has been completed.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
      </div>
    </div>
  </section>
@endsection