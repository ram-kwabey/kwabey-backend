@extends('layouts.app')

@section('content')
<div class="container-fluid px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
  <div class="card card0 border-0">
    <div class="row d-flex">
      <div class="col-xl-6">
        <div class="card1 pb-5">
          <div class="row"> <img src="{{ asset('admin/img/kwabey-logo.png') }}" class="logo img-fluid"> </div>
          <div class="row px-3 justify-content-center mt-4  border-line"> <img src="{{ asset('admin/img/delivery.png') }}" class="image img-fluid "> </div>
        </div>
      </div>
      <div class="col-xl-6 pb-4">
        <div class="card2 card border-0 mrT">
          <div class="row mb-4 px-3">
            <h1 class="mb-0 mr-4 mt-2 welcometxt">Reset Password</h1>
          </div>
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                        @csrf
              <div class="row px-3 input-icons">
                <label class="mb-1">
                <h6 class="mb-0 text-sm">Email Address</h6>
                </label>
                <i class="fa fa-user icon"></i>
                <input id="email" type="email" class="mb-4 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter a valid email address">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
              </div>
              
              <div class="row px-3 mb-4">
                @if (Route::has('login'))
                <a href="{{ route('login') }}" class="ml-auto mb-0 text-sm">Login User?</a> 
                @endif
              </div>
              <div class="row mb-3 px-3">
                <button type="submit" class="btn log-in btn-success text-center">Send Password Reset Link</button>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
