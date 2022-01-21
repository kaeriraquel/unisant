@extends('layouts.app', ['class' => 'off-canvas-sidebar', 'activePage' => 'login', 'title' => __('Material Dashboard')])
@section('styles')
  <link rel="stylesheet" href="/css/stars.css">
@endsection
@section('content')
<div id='stars'></div>
<div id='stars2'></div>
<div id='stars3'></div>
<div class="container" style="height: auto;">
  <div class="row align-items-center">
    <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
      <div class="row">
        <div class="col-3"></div>
        <div class="col-6 text-center">
          <img src="{{asset("images/logo_white.png")}}" class="img-fluid">
        </div>
      </div>
      <form class="form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="card card-login card-hidden mb-3" style="background:transparent;">
          <div class="card-body" style="background:none;">
            <div class="bmd-form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="material-icons text-light">email</i>
                  </span>
                </div>
                <input type="email" name="email" class="form-control text-dark" placeholder="Correo electrónico" value="{{ old('email', '') }}" required>
              </div>
              @if ($errors->has('email'))
                <div id="email-error" class="error text-danger pl-3" for="email" style="display: block;">
                  <strong>{{ $errors->first('email') }}</strong>
                </div>
              @endif
            </div>
            <div class="bmd-form-group{{ $errors->has('password') ? ' has-danger' : '' }} mt-3">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="material-icons text-light">lock_outline</i>
                  </span>
                </div>
                <input type="password" name="password" id="password" class="form-control  text-dark" placeholder="Clave" value="{{ !$errors->has('password') ? "" : "" }}" required>
              </div>
              @if ($errors->has('password'))
                <div id="password-error" class="error text-danger pl-3" for="password" style="display: block;">
                  <strong>{{ $errors->first('password') }}</strong>
                </div>
              @endif
            </div>
            <div class="form-check mr-auto ml-3 mt-3">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme
                <span class="form-check-sign">
                  <span class="check" style="border-color:white;"></span>
                </span>
              </label>
            </div>
          </div>
          <div class="card-footer justify-content-center">
            <button type="submit" class="btn btn-light btn-lg">Ingresar</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection
