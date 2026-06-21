@extends('layouts.app')

@section('title', $PageMeta->title)
@section('description', $PageMeta->description)
@section('keywords', $PageMeta->keywords)

@section('content')
  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12 text-center">
          <h2 class="title">Contact Us</h2>
          <h5 class="description">Please don’t hesitate to contact us!</h5>
          <h5 class="description">We are really interested in your message.</h5>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-md-8 mx-auto">
          <div class="card rounded-0">
            <div class="card-header">
              <h3 class="mb-0">Contact US</h3>
            </div>

            <div class="card-body">
              @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
              @endif

              @if (isset($errors) && is_object($errors) && method_exists($errors, 'any') && $errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form method="POST" action="{{ route('contact.store') }}">
                @csrf

                {{-- honeypot --}}
                <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                <div class="mt-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                    </div>
                    <input type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}"
                      required>
                  </div>
                </div>

                <div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                    </div>
                    <input type="email" name="email" class="form-control" placeholder="Email address"
                      value="{{ old('email') }}" required>
                  </div>
                </div>

                <div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa-regular fa-file-lines"></i></span>
                    </div>
                    <textarea class="form-control" rows="6" name="message" placeholder="Type your message"
                      required>{{ old('message') }}</textarea>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="mt-4 btn btn-primary btn-round btn-lg">SEND</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
@endsection