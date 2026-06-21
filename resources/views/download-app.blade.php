@extends('layouts.app')

@section('title', $PageMeta->title)
@section('description', $PageMeta->description)
@section('keywords', $PageMeta->keywords)

@section('content')

  <div class="section section-nucleo-icons">
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-9 m-auto text-center">
          <h2 class="title">Download Free Indexer Desktop App</h2>
          <h5 class="description">Download using the following link, follow the below steps to install the program!</h5>
          <div class="mt-5">
            <a href="./app/free_indexer.exe" class="boxtn btn-primary btn-round btn-lg  text-decoration-none" download>
              <i class="fas fa-arrow-alt-circle-down"></i> Start Download!
            </a>
          </div>
        </div>
      </div>
      <hr class="mt-5 mb-5">
      <div class="row mt-5">
        <div class="col-lg-9 m-auto text-center">
          <h5 class="description mt-5">After you download the setup file, the smart screen may be triggered by Windows
            (Windows 8 or newer) because the installation file is still new and not recognized by Windows. You just ignore
            the warning and proceed to the installation by following the steps below:</h5>
          <img class="img-fluid" src="{{ asset("assets/images/windows_step_1.jpg") }}">
          <h5 class="description mt-5">If Windows SmartScreen pops up: Click on More Info</h5>
          <img class="img-fluid" src="{{ asset("assets/images/windows_step_2.jpg") }}">
          <h5 class="description mt-5">Run anyway</h5>
          <img class="img-fluid" src="{{ asset("assets/images/windows_step_3.jpg") }}">
        </div>
      </div>
    </div>
  </div>

@endsection