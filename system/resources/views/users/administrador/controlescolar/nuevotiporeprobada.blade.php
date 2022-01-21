@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <form class="form" action="/controlescolar/tiposdereprobadas" method="post">
@php
  $menu = ["Regresar" => "/controlescolar/tiposdereprobadas"];
@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Nuevo tipo de materia reprobada</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="{{$menu["Regresar"]}}">Regresar</a>
      </div>
    </div>
    <hr>
        @csrf
        <div class="row">
          <div class="col">
            <label class="text-dark" for="">Tipo de materia reprobada:</label>
            <input class="form-control enable allow" required type="text" name="name" placeholder="Nombre del tipo de materia reprobada">
          </div>
        </div>
      <hr>
      <a href="/controlescolar/tiposdereprobadas" class="btn btn-link text-danger">
        Cancelar
      </a>
      <button class="btn btn-primary" type="submit" name="button">
        <i class="fa fa-save"></i>
        Guardar
      </button>
    </div>
  </div>
</form>

@endsection
@section('scripts')
  <script type="text/javascript">

  </script>
@endsection
