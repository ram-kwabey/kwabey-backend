@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center hold-transition">
        <div class="col-md-4">
        <div class="login-box">
            <!-- /.login-logo -->
            <div class="login-logo">
                <a href="javascript:void(0)" style="color:#495057; text-decoration: none;"><b>{{ config('app.name', 'Kwabey') }}</a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                <p class="login-box-msg" style="margin: 0;padding: 0 20px 20px;text-align: center;font-size:1rem;">
                    Sign in to start your session</p>
                <form method="POST" action="{{ route('login') }}">
                        @csrf
                    <div class="input-group mb-3">
                    <input id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
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
                    <input id="password" placeholder="Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

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
                    <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" style="font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">
                            Remember Me
                        </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                    </div>
                </form>

                <!-- /.social-auth-links -->

                @if (Route::has('password.request'))
                <p class="mb-1 mt-4">
                    <a class="text-center" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </p>
                @endif
                @if (Route::has('register'))
                <!-- <p class="mb-1">
                    <a class="text-center" href="{{ route('register') }}">{{ __('Register a new memebership') }}</a>
                </p> -->
                @endif
                
                    
                
    
                
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        </div>
    </div>
</div>
<script type="text/javascript">
localStorage.removeItem('isModalVisible');
</script>
@endsection
