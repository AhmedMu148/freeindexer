@extends('layouts.app')

@section('title', $PageMeta->title)
@section('description', $PageMeta->description)
@section('keywords', $PageMeta->keywords)

@section('content')

  <div class="section section-nucleo-icons">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-9">
          <h2 class="title">Free Indexer App</h2>
          <h5 class="description">Simple to use, yet powerful and flexible link indexer software!
            <br>
            Free Indexer is the fastest software tool for backlinks indexing, it produces the finest indexes with
            remarkable efficiency and 10x faster than other alternative software. It is a one-time purchase with free
            lifetime updates!
          </h5>
          <div id="getstarted-section" class="mt-5">

            <div class="text-20">PRICE <b> $
            @php
              if ($appPlan->trial == 0 || $freeApp) {
                if ($appPlan->price_offer != 0.00) {
                  echo "<s>" . $appPlan->price . "</s>/" . $price;
                } else {
                  echo $price;
                }
              } else {
                echo '<b>Free Trial</b>';
              }
            @endphp
            </b></div>

            <br>
            @auth()

              @if ($appPlan->trial == 0 || $freeApp)
              <form action="{{ route('billing.subscribe') }}" method="post">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $appPlan->id }}">
                <button type="submit" class="boxtn btn-primary btn-round btn-l btn-lg border-0 text-decoration-none">
                  <i class="fas fa-check-circle"></i> Get Started
                </button>
              </form>
                @else
                <a class='boxtn btn-primary btn-round btn-lg action' url='app.php' data='{"action":"free_trial","uid":"<?= $uid ?>","trial":"<?= $trial ?>"}' href='javascript:;'><i class="fas fa-check-circle"></i> Get Started</a>
              @endif
              
            @else
              <a href="{{ route('login') }}" class="boxtn btn-primary btn-round btn-l btn-lg text-decoration-none">
                <i class="fas fa-check-circle"></i> Get Started
              </a>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="section section-tabs">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-12 mt-5">
          <h2 class="title">Get your Websites Indexed by Google More Quickly</h2>
          <h5 class="description">
            Don't waste Your valuable time searching the web for any Indexing tools.
            <br>
            Just download the Free Indexer App and it will do the rest. Free Indexer App will do all the hard work. You
            don't need to worry about the technical details. We help you automate your Backlinks Indexing process to save
            hundreds of hours of manual work - time that could be spent on productive and enjoyable tasks.
          </h5>
        </div>
        <div class="col-lg-6 col-md-12 text-center">
          <img class="img-fluid" src="{{ asset("assets/images/freeindexer_app_package.png") }}" width="400px"
            alt="FreeIndexer link-indexing app">
        </div>
      </div>
      <div class="mt-5 text-center d-flex justify-content-center align-items-center gap-3">
        <a href="{{ route('download-app') }}" class="boxtn btn-info btn-round btn-l me-2 btn-lg text-decoration-none">
          <i class="fas fa-download"></i> Download
        </a>
        @auth()
        <form action="{{ route('billing.subscribe') }}" method="post">
          @csrf
          <input type="hidden" name="plan_id" value="{{ $appPlan->id }}">
          <button type="submit" class="boxtn btn-primary btn-round btn-l btn-lg border-0 text-decoration-none">
            <i class="fas fa-check-circle"></i> Buy Now
          </button>
        </form>
        @else
        <a href="{{ route('login') }}" class="boxtn btn-primary btn-round btn-l btn-lg text-decoration-none">
          <i class="fas fa-check-circle"></i> Buy Now
        </a>
        @endauth
      </div>
    </div>
  </div>

  <div class="section section-nucleo-icons">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-12">
          <img class="img-fluid mb-2" src="{{ asset("assets/images/freeindexer_desktop.gif") }}"
            alt="FreeIndexer user interface">
        </div>
        <div class="col-lg-6 col-md-12">
          <h2 class="title">Modern, Simple and Easy Interface</h2>
          <h5 class="description">
            You will get used to the program within seconds. SELECT your links, then START your campaign and Forget. Free
            Indexer app will submit each one of your Backlinks to hundreds of statistical sites resulting in many
            backlinks.
            <div>
              <i class="far fa-check-circle mt-3"></i> Super Fast Results in Just a Few Clicks!
              <br>
              <i class="far fa-check-circle mt-3"></i> 10x faster than other alternative software.
              <br>
              <i class="far fa-check-circle mt-3"></i> One time payment with lifetime updates (NO monthly or yearly
              subscription).
              <br>
              <i class="far fa-check-circle mt-3"></i> Compatible with all windows and windows server versions.
            </div>
          </h5>
        </div>
      </div>
    </div>
  </div>

@endsection