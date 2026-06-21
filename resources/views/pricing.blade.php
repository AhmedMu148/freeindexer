@extends('layouts.app')

@section('title', $PageMeta->title)
@section('description', $PageMeta->description)
@section('keywords', $PageMeta->keywords)

@section('content')

  <section id="section1">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        @auth()
          <h4 class="text-center py-3">You Now Have <b>1000</b> Daily points for indexer</h4>
        @else
          <h4 class="text-center py-3">You Can
            <a href="{{ route('register') }}" class="btn btn-warning btn-round">SIGN UP</a>
            With Us And Get <b>1000</b> Daily points for indexer
          </h4>
          @endauth
        </div>
      </div>
    </div>
  </section>

  <section id="section2" class="section section-tabs py-7">
    <div class="container">
      <h2 class="text-center display-4 pb-3">Monthly Plans</h2>
      <div class="row">

        @foreach ($monthlyPlans as $monthlyPlan)
          <div class="col-md-4">
            <div class="block block-pricing py-4">
              <div class="table">
                <h6 class="category">{{ $monthlyPlan->name }}</h6>
                <div class="icon mt-3 mb-5">
                  <p class="m-auto">${{ $monthlyPlan->price }}</p>
                </div>
                <p class="block-description"><b>{{ number_format($monthlyPlan->indexer) }}</b> Daily points for indexer</p>
                <p class="block-description"><b>{{ number_format($monthlyPlan->bg_indexer) }}</b> Daily points for Background indexer</p>
                <p class="block-description"><b>{{ number_format($monthlyPlan->backlinks) }}</b> Daily points for Backlinks<br>(MIX Platforms)</p>
                  @auth()
                    <form action="{{ route('billing.subscribe') }}" method="post">
                      @csrf
                      <input type="hidden" name="plan_id" value="{{ $monthlyPlan->id }}">
                      <button type="submit" class="btn btn-warning btn-round">Subscribe</button>
                    </form>
                  @else
                  <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn btn-warning btn-round">Subscribe</a>
                  @endauth
              </div>
            </div>
          </div>
        @endforeach

      </div>
    </div>
  </section>

@endsection