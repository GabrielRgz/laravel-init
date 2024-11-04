@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Register Page')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center">
      <div class="flex-row text-center mx-auto">
        {{-- <img src="{{asset('assets/img/pages/register-'.$configData['style'].'.png')}}" alt="Auth Cover Bg color" width="520" class="img-fluid authentication-cover-img" data-app-light-img="pages/register-light.png" data-app-dark-img="pages/register-dark.png"> --}}
        <div class="mx-auto">
          <h3>Registro de usuarios 🙍🏻‍♂️ </h3>
          <p>
            Pantalla de registro temporal <br>
          </p>
        </div>
      </div>
    </div>
    <!-- /Left Text -->

    <!-- Register Card -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand mb-4">
          <a href="{{url('/')}}" class="app-brand-link gap-2 mb-2">
            <span class="app-brand-logo demo">@include('_partials.macros')</span>
            <span class="app-brand-text demo h3 mb-0 fw-bold">{{config('variables.templateName')}}</span>
          </a>
        </div>
        <!-- /Logo -->

        <!-- Register Card -->
        <h4 class="mb-2">Nuevo usuario</h4>
        <p class="mb-4">Llenar los campos con los datos correspondientes</p>

        <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="username" name="name" placeholder="johndoe" autofocus value="{{ old('name') }}" />
            @error('name')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="john@example.com" value="{{ old('email') }}" />
            @error('email')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
              <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer">
                <i class="bx bx-hide"></i>
              </span>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>

          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password-confirm">Confirm Password</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password-confirm" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
              <span class="input-group-text cursor-pointer">
                <i class="bx bx-hide"></i>
              </span>
            </div>
          </div>
          @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
          <div class="mb-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="terms" name="terms" />
              <label class="form-check-label" for="terms">
                I agree to the
                <a href="{{ route('terms.show') }}" target="_blank">
                  terms_of_service
                </a> and
                <a href="{{ route('policy.show') }}" target="_blank">
                  privacy_policy
                </a>
              </label>
            </div>
          </div>
          @endif
          <button type="submit" class="btn btn-primary d-grid w-100">Sign up</button>
        </form>

        <p class="text-center mt-2">
          <span>Ya tienes una cuenta?</span>
          @if (Route::has('login'))
          <a href="{{ route('login') }}">
            <span>INICIAR SESION</span>
          </a>
          @endif
        </p>
      </div>
    </div>
    <!-- Register Card -->
  </div>
</div>
@endsection