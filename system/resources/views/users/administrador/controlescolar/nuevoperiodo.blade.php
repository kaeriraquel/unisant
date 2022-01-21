@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <form class="form" action="/controlescolar/periodos" method="post">
@php
  $menu = ["Regresar" => "/controlescolar/periodos"];
@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Nuevo periodo</h3>
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
            <label class="text-dark" for="">Nombre del periodo:</label>
            <input class="form-control enable allow" required type="text" name="periodo" placeholder="Nombre del periodo">
            <label class="text-dark" for="">Clave:</label>
            <input class="form-control enable allow" required type="text" name="clave" placeholder="2021A">
            <label class="text-dark" for="">Fecha de inicio:</label>
            <input class="form-control enable allow" required type="date" name="fecha_inicio" placeholder="2011/12/01">
            <label class="text-dark" for="">Fecha de termino:</label>
            <input class="form-control enable allow" required type="date" name="fecha_termino" placeholder="2011/12/01">
            <label class="text-dark">Pertenece a la sede:</label>
            <select class="form-control allow" required name="sede_id">
              <option value="">Seleccionar</option>
              @foreach (\App\sedes::all() as $sede)
                <option value="{{$sede->id}}">{{$sede->sede}}</option>
              @endforeach
            </select>
          </div>
        </div>
      <hr>
      <a href="/controlescolar/periodos" class="btn btn-link text-danger">
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
