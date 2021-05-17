@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-4">
            <div class="login-logo">
                <a href="javascript:void(0)" style="color:#495057; text-decoration: none;"><b>{{ config('app.name', 'Laravel') }}</a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                <p class="login-box-msg" style="margin: 0;padding: 0 20px 20px;text-align: center;font-size:1rem;">
                    Register a new membership</p>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                <div class="input-group mb-3">
                <input 
                    id="name" 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror"    
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    autocomplete="name" 
                    autofocus
                    placeholder="Full name" 
                    pattern="[A-Za-z\s]+"
                    oninvalid="setCustomValidity('Full Name should only contain letters. e.g. john smith')"
                    onchange="try{setCustomValidity('')}catch(e){}"
                >
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-user"></span>
                    </div>
                </div>
                @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-group mb-3">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="email">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                    </div>
                </div>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-group mb-3">
                <input id="password" type="password" placeholder="Password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                    </div>
                </div>
                @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-group mb-3">
                <input id="password-confirm" type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                <div class="input-group-append">
                    <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                    </div>
                </div>
                </div>
                <div class="row">
                <!-- /.col -->
                <div class="col-12 clearfix">
                    <button type="submit" class="btn btn-primary float-right">Register</button>
                </div>
                <!-- /.col -->
                </div>
                </form>
                @if (Route::has('login'))
                <p class="mb-1 mt-3">
                    <a class="text-center" href="{{ route('login') }}">{{ __('I already have a membership') }}</a>
                </p>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
