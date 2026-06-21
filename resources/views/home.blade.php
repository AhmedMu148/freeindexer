@extends('layouts.app')

@section('title', $PageMeta->title)
@section('description', $PageMeta->description)
@section('keywords', $PageMeta->keywords)

@section('content')

  @auth
    <a class="btn btn-primary btn-side rounded-0" href="{{ \Filament\Pages\Dashboard::getUrl(panel: 'dashboard') }}"
      style="margin: 80px 1px;">Back To
      Dashboard</a>
  @endauth

  <section class="section-hero section-main">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-12 text-center">
          <h1 class="title">Free Indexer V2.5</h1>
          <p class="description">Free Indexer submits each one of your URLs “backlinks” to hundreds of statistical sites.
            <br>This gives you too many backlinks or link pyramids. And get your URLs “backlinks” indexed by Google!
          </p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-12 col-sm-12 ms-auto me-auto text-center">

          <form action="{{ route('indexer.processPage') }}" method="post">

            @csrf

            <div class="bg-info my-3 p-3 text-white" id="pointsBox" data-indexer-points="{{ (int) $userIndexerPoints }}"
              data-email-verified="{{ (!auth()->check() || auth()->user()->hasVerifiedEmail()) ? '1' : '0' }}">
              <i class="fa fa-info-circle" aria-hidden="true"></i>
              Your Points For Link Indexer = {{ $userIndexerPoints }}
              - To Access More Points <a class='text-dark' href="{{ route('pricing') }}"><ins>Click Here</ins></a>
            </div>

            <div class="textarea-container">
              <textarea class="form-control" id="user_urls" name="user_urls" rows="8" cols="80"
                placeholder="Enter all URLs here with line break between each URL"></textarea>
              <input class="form-control" id="domain_url" name="domain_url" style="display:none"
                placeholder="Enter Your Domain Url">
            </div>

            @guest
              <div class="bg-warning my-3 p-3 text-white">
                <i class="fa fa-bell" aria-hidden="true"></i>
                Full List option disabled for guests
                <a href="{{ route('login') }}" class="text-dark">
                  <b>Please login!</b>
                </a>
              </div>
            @endguest

            <div class="row mb-3 text-start">
              <div class="col-md-4">
                <div class="domain-indexer float-start mb-2">
                  <input id="domain" class="minimal" type="checkbox" name="domain-indexer" value="domain"> Domain Indexer
                </div>
              </div>
              <div class="col-md-2">
                <input type="radio" class="minimal" name="list-type" value="quick-list" checked> Quick List
              </div>
              <div class="col-md-2">
                <input type="radio" class="minimal" name="list-type" value="full-list" @guest disabled @endguest>
                Full List
              </div>
            </div>

            <input type="submit" id="submitBtn" name="submit" value="Process Now"
              class="btn btn-primary btn-round btn-lg">

            <div>
              @error('user_urls')
                <div class="alert alert-danger">{{ $message }}</div>
              @enderror
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>

  <section class="section section-about-us mt-5 pb-0">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="title">What is FREE INDEXER?</h2>
          <h5 class="description my-0">"Free Indexer" is a combined software kit, it is focused on getting
            websites/weblinks
            &amp; backlinks to get indexed by Google, Bing and other search engines. Free Indexer also integrates with
            other 3rd party indexer engines in order to deliver a full indexing solution. So whatever your strategy/plan
            you will find the proper link indexing solutions!</h5>
        </div>
      </div>
    </div>
  </section>

  <div class="separator separator-primary my-5"></div>

  <section class="">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="title">How does it work?</h2>
          <h5 class="description my-0">Rapid Indexer submits your URLs to various statistical sites. These sites give a
            value
            of your URLs and also provide a free link back to your site. Our rapid indexer sends your URLs to over 15,000
            sites which gives you that many one way backlinks and Rapidly gets your URLs indexed by Google!</h5>
        </div>
      </div>
    </div>
  </section>

  <div class="separator separator-primary my-5"></div>

  <section class="section section-about-us py-0 mb-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="title">Our partners</h2>
          <h5>
            <a href="https://panel.seoestore.net/press-release.php" class="text-decoration-none" target="_blank">Get
              featured on 200+ news sites</a>
            <span class="badge badge-success">100% indexed</span>
          </h5>
          <p>
            Check out <a href="https://captchaai.com/" class="text-decoration-none" target="_blank">"reCAPTCHA",
              "hCaptcha" OCR Solver by CaptchaAI</a>
          </p>
        </div>
      </div>
    </div>
  </section>

  @push('scripts')
    <script>
      $(function () {
        $('.domain-indexer').on('click', function () {
          if ($('#domain').is(':checked')) {
            $("#user_urls").hide();
            $("#domain_url").fadeIn();
          } else {
            $("#user_urls").fadeIn();
            $("#domain_url").hide();
          }
        })
        $('#domain').on('ifChecked', function (e) {
          $("#user_urls").hide();
          $("#domain_url").fadeIn();
        })
        $('#domain').on('ifUnchecked', function (e) {
          $("#user_urls").fadeIn();
          $("#domain_url").hide();
        })
      })

      const HARD_CAP = 5000;
      function getPoints() {
        return parseInt(
          document.getElementById('pointsBox')?.dataset?.indexerPoints || '0',
          10
        );
      }
      function getAllowedLimit() {
        return Math.min(getPoints(), HARD_CAP);
      }
      function countValidLines(text) {
        return text
          .split('\n')
          .map(l => l.trim())
          .filter(l => l.length > 0)
          .length;
      }
      function isDomainMode() {
        return document.getElementById('domain')?.checked === true;
      }
      function isEmailVerified() {
        return document.getElementById('pointsBox')?.dataset?.emailVerified === '1';
      }
      function validateForm() {
        const submitBtn = document.getElementById('submitBtn');
        if (!isEmailVerified()) {
          submitBtn.disabled = true;
          return;
        }
        const points = getPoints();
        const textarea = document.getElementById('user_urls');
        const domainInput = document.getElementById('domain_url');
        if (points <= 0) {
          submitBtn.disabled = true;
          return;
        }
        // Domain Indexer mode
        if (isDomainMode()) {
          const domainVal = (domainInput.value || '').trim();
          if (domainVal.length === 0) {
            submitBtn.disabled = true;
            return;
          }
          submitBtn.disabled = false;
          return;
        }
        // URLs mode
        const allowed = getAllowedLimit();
        const current = countValidLines(textarea.value);
        if (current <= 0) {
          submitBtn.disabled = true;
          return;
        }
        if (current > allowed) {
          submitBtn.disabled = true;
          document.querySelector('#limitModal .modal-body').textContent =
            `Maximum ${allowed} URLs allowed per submission.`;
          new bootstrap.Modal(document.getElementById('limitModal')).show();
          return;
        }
        submitBtn.disabled = false;
      }
      document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const textarea = document.getElementById('user_urls');
        const domainInput = document.getElementById('domain_url');
        const domainCheckbox = document.getElementById('domain');
        textarea.addEventListener('input', validateForm);
        domainInput.addEventListener('input', validateForm);
        domainCheckbox.addEventListener('change', validateForm);
        validateForm();
        form.addEventListener('submit', function (e) {
          validateForm();
          if (document.getElementById('submitBtn').disabled) {
            e.preventDefault();
          }
        });
      });
    </script>

    <!-- Modal -->
    <div class="modal fade" id="limitModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Alert</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body mb-3">
            Maximum 5000 URLs are allowed in one submission
          </div>
          <div class="modal-footer justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  @endpush

@endsection