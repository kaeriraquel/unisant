@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <form class="form" action="/controlescolar/estadodelalumno" method="post">
@php
  $menu = ["Regresar" => "/controlescolar/estadosdelalumno"];
@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Nuevo estado del alumno</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="{{$menu["Regresar"]}}">Regresar</a>
      </div>
    </div>
    <hr>
        @csrf
        <div class="row">
          <div class="col-4">
            <label class="text-dark" for="">Nombre del estado:</label>
            <input class="form-control enable allow" required type="text" name="name" placeholder="Nombre del estado del alumno">
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <label class="text-dark" for="">Color de fondo:</label>
            <br>
            <input name="background" type="color" value="#FFFFFF">
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <label class="text-dark" for="">Color del texto:</label>
            <br>
            <input name="color" type="color" value="#000000">
          </div>
        </div>
      <hr>
      <div class="clearfix">
        <div class="float-left">
          <button class="btn btn-primary" type="submit" name="button">
            <i class="fa fa-save"></i>
            Guardar
          </button>
        </div>
        <div class="float-right">
          <a href="{{$menu["Regresar"]}}" class="btn btn-link text-danger">
            Cancelar
          </a>
        </div>
      </div>
    </div>
  </div>
</form>

@endsection
@section('scripts')
  <script type="text/javascript">

  </script>
@endsection
