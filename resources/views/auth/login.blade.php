@extends('layouts/blankLayout')

@section('title', 'Login')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic" style="min-height: 100vh;">
  <div class="d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-5 col-xl-4 p-4 mx-auto login-card w-100"> <!-- Asegura que la tarjeta use todo el ancho -->
          <div class="w-px-400 mx-auto">
                <!-- Logo -->
                <div class="app-brand justify-content-center mb-4 text-center">
                    <a href="{{url('/')}}" class="app-brand-link gap-2 mb-2">
                        <span class="app-brand-text demo h3 mb-0 fw-bold">{{config('variables.templateName')}}</span>
                    </a>
                </div>
                <!-- /Logo -->
                <h4 class="mb-2">Bienvenido al sistema {{config('variables.templateName')}}!</h4>
                <p class="mb-4">Por favor inicia sesión para comenzar</p>

                @if (session('status'))
                <div class="alert alert-success mb-1 rounded-0" role="alert">
                    <div class="alert-body">
                        {{ session('status') }}
                    </div>
                </div>
                @endif

                <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="login-email" class="form-label">Email</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email" placeholder="john@example.com" autofocus value="{{ old('email') }}">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="mb-3 form-password-toggle">
                        <div class="d-flex justify-content-between">
                            <label class="form-label" for="login-password">Contraseña</label>
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                <small>¿Olvidaste tu contraseña?</small>
                            </a>
                            @endif
                        </div>
                        <div class="input-group input-group-merge">
                            <input type="password" id="login-password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember-me">
                                Recordar sesión
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100" type="submit">Iniciar sesión</button>
                </form>

                <p class="text-center">
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}">
                        <span>Crear cuenta</span>
                    </a>
                    @endif
                </p>
            </div>
        </div>
        <!-- /Login -->
    </div>
</div>
@endsection
