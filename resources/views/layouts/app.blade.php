<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>
    @hasSection('title')
      @yield('title')
    @else
      {{ config('app.name', 'Free Indexer') }}
    @endif
  </title>
  <meta name="description" content="@yield('description')">
  <meta name="keywords" content="@yield('keywords')">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  {{--
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}

  <script>
    $.ajaxSetup({
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
  </script>

  <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/images/apple-icon.png') }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
    integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    referrerpolicy="no-referrer" />

  <!-- iCheck 1.0.1 -->
  <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

  <link rel="stylesheet" href="{{ asset('assets/css/style.css?v=3') }}">

</head>

<body>

  <!-- Navigation -->
  @include('layouts.nav')

  <!-- Page Content -->
  @yield('content')

  <!-- Footer -->
  @include('layouts.footer')

  <meta name="csrf-token" content="{{ csrf_token() }}">

  @stack('scripts')

</body>

</html>