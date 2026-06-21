<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ route('/') }}">
            <img class="logo" src="{{ asset('assets/images/logo.png') }}" alt="Free Indexer">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item {{ request()->routeIs('/') ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('/') }}">
                        <i class="fontawesome fa-regular fa-house"></i>
                        <span class="ms-1">Home</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" aria-current="page"
                        href="{{ \Filament\Pages\Dashboard::getUrl(panel: 'dashboard') }}">
                        <i class="fontawesome fa-solid fa-grip"></i>
                        <span class="ms-1">Dashboard</span>
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tickets.sso') }}" target="_blank">
                            <i class="fontawesome fa-solid fa-ticket"></i>
                            <span class="ms-1">Tickets</span>
                        </a>
                    </li>
                @endauth

                <li class="nav-item {{ request()->routeIs('pricing') ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('pricing') }}">
                        <i class="fontawesome fa-regular fa-money-bill-1"></i>
                        <span class="ms-1">Pricing</span>
                    </a>
                </li>

                <li
                    class="nav-item {{ request()->routeIs('buy-app') || request()->routeIs('download-app') ? ' active' : '' }}">
                    <a class="nav-link" href="{{ route('buy-app') }}">
                        <i class="fontawesome fa-regular fa-circle-down"></i>
                        <span class="ms-1">Download App</span>
                    </a>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-circle-user"></i>
                            <span class="ms-1">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu p-0" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item py-2" href="{{ route('profile') }}">Profile</a></li>
                            <div class="dropdown-divider p-0 m-0"></div>
                            <li><a class="dropdown-item py-2" href="{{ route('tickets.sso') }}">My Tickets</a></li>
                            <div class="dropdown-divider p-0 m-0"></div>
                            <li>
                                <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()"
                                    class="dropdown-item py-2">
                                    Sign out
                                </a>
                                <form id="logout-form" action="{{ route('filament.dashboard.auth.logout') }}"
                                    method="POST" class="hidden">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fontawesome fa-solid fa-plus"></i>
                            <span class=" ms-1">Register</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fontawesome fa-solid fa-arrow-right-to-bracket"></i>
                            <span class="ms-1">Login</span>
                        </a>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>

@if (session('status') === 'verification-link-sent')
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Verification email sent successfully.
            Please check your inbox (and Spam/Junk).
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@auth
    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
        <div class="container mt-3">
            <div
                class="alert alert-warning d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div>
                    <h6 class="alert-heading mb-1">
                        Please verify your email address
                    </h6>
                    <p class="mb-0">
                        We sent a verification link to
                        <strong>{{ auth()->user()->email }}</strong>.
                        Please check your inbox (and Spam/Junk).
                    </p>
                </div>

                <form method="POST" action="{{ route('filament.dashboard.auth.email-verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">
                        Resend link
                    </button>
                </form>
            </div>
        </div>
    @endif
@endauth

{{-- <nav id="header-nav" class="navbar navbar-expand-lg bg-primary"> --}}
{{-- <div class="container"> --}}
{{-- <div class="navbar-translate"> --}}
{{-- <a class="navbar-brand" href="{{ route('/') }}">
        <img class="logo" src="https://freeindexer.com/tpl/img/logo.png" alt="Free Indexer">
      </a> --}}
{{-- <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
        data-target="#example-navbar-primary" aria-controls="navbarSupportedContent" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-bar bar1"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </button> --}}
{{-- </div> --}}


{{-- <div class="collapse navbar-collapse justify-content-end" id="example-navbar-primary">
      <ul class="navbar-nav">
        <li id="dashboard" class="nav-item">
          <a class="nav-link" href="https://freeindexer.com/dashboard/index.php">
            <i class="now-ui-icons design_app"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li id="pricing" class="nav-item ">
          <a class="nav-link" href="https://freeindexer.com/pricing.php">
            <i class="now-ui-icons business_money-coins"></i>
            <p>Pricing</p>
          </a>
        </li>
        <li id="download" class="nav-item ">
          <a class="nav-link" href="https://freeindexer.com/buy-app.php">
            <i class="now-ui-icons shopping_box"></i>
            <p>Download App</p>
          </a>
        </li>
        <li id="third_party" class="nav-item ">
          <a class="nav-link" href="https://freeindexer.com/third_party.php">
            <i class="fa fa-cubes" aria-hidden="true"></i>
            <p>Third Party</p>
          </a>
        </li>
        <!-- Show these items if the user is not logged in -->
        <li id="register" class="nav-item ">
          <a class="nav-link" href="https://freeindexer.com/register.php?url=index.php">
            <i class="now-ui-icons ui-1_simple-add"></i>
            <p>Register</p>
          </a>
        </li>
        <li id="login" class="nav-item ">
          <a class="nav-link" href="https://freeindexer.com/login.php?url=index.php">
            <i class="now-ui-icons users_circle-08"></i>
            <p>Login</p>
          </a>
        </li>
      </ul>
    </div> --}}


{{--
  </div> --}}
{{-- </nav> --}}
