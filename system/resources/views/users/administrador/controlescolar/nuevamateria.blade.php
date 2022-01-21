@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
@php
  $menu = ["Regresar" => "/controlescolar/nuevo?cid=".Request::get("cid")];
  $revo = Request::get("cid") ? \App\revoes::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
  $materia = NULL;
  if (Request::has("edit")) {
    $materia = \App\materias::whereRAW("md5(id)='".Request::get("edit")."'")->first();
  }
@endphp
<form class="form" action="/controlescolar/{{$materia == NULL ? "materias" : "actualizarmaterias"}}" method="post">
  @if ($materia != NULL)
    <input type="hidden" name="cid" value="{{md5($materia->id)}}">
  @endif
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>{{$materia == NULL ? "Nueva" : "Editar"}} materia</h3>
        <h4>
          <small>
            para
          </small>
          {{$revo->nombre}}
        </h4>
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
            <label class="text-dark" for="">Nombre:</label>
            <input class="form-control enable allow" required type="text" name="name" value="{{$materia == NULL ? "" : $materia->name}}" placeholder="EVALUACIÓN BASADA EN UN MODELO POR COMPETENCIAS">
            <label class="text-dark" for="">Clave:</label>
            <input class="form-control enable allow" required type="text" name="clave" value="{{$materia == NULL ? "" : $materia->clave}}" placeholder="602">
            <label class="text-dark" for="">Seriación:</label>
            <input class="form-control enable allow" required type="number" min="0" value="{{$materia == NULL ? "" : $materia->seriacion}}" name="seriacion" placeholder="102">
            <label class="text-dark" for="">Número de materia:</label>
            <input class="form-control enable allow" required type="number" step="1" min="1" value="{{$materia == NULL ? "" : $materia->numero}}" name="numero" placeholder="12">
            <label class="text-dark" for="">Créditos:</label>
            <input class="form-control enable allow" required type="number" step="1" min="1" name="creditos" value="{{$materia == NULL ? "" : $materia->creditos}}" placeholder="10">
            <label class="text-dark" for="">Carrera:</label>
            <input type="hidden" name="rvoe_id" value="{{$revo->id}}">
            <div class="form-control">
              {{$revo->nombre}}
            </div>
            <label class="text-dark" for="">Tipo de materia:</label>
            <select class="form-control allow" name="tipomateria_id">
              <option value="">Seleccionar</option>
              @foreach (\App\tiposdematerias::where("deleted_at",NULL)->get() as $car)
                <option {{$materia == NULL ? "" : ($car->id == $materia->tipomateria_id ? "selected" : "")}} value="{{$car->id}}">{{$car->name}}</option>
              @endforeach
            </select>
            <label class="text-dark" for="">Plan escolar:</label>
            <select class="form-control allow" required name="planescolar_id">
              <option value="">Seleccionar</option>
              @foreach (\App\planescolar::all() as $car)
                <option {{$materia == NULL ? "" : ($car->id == $materia->planescolar_id ? "selected" : "")}} value="{{$car->id}}">{{$car->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
      <hr>
      <a href="/controlescolar/nuevo?cid={{Request::get("cid")}}" class="btn btn-link text-danger">
        Cancelar
      </a>
      <button class="btn btn-primary" type="submit" name="button">
        <i class="fa fa-save"></i>
        {{$materia == NULL ? "Guardar" : "Actualizar"}}
      </button>
    </div>
  </div>
</form>

@endsection
@section('scripts')
  <script type="text/javascript">
      $(".selecta").bind("change",function(){
        $(".what").text($("select option:selected").val());
        if($("select option:selected").val() == "Monto fijo por"){
          $(".conceptopago").removeClass("d-none").find("select").attr("required","true");
        } else {
          $(".conceptopago").addClass("d-none").find("select").removeAttr("required");
        }
      });
  </script>
@endsection
