@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <form class="form" action="/dist/nuevoconcepto" method="post">
@php
  $menu = ["Regresar" => "/pagos/distribuir?cid=".Request::get("cid")];
@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Nuevo concepto de distribución</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="/pagos/distribuir?cid={{Request::get("cid")}}">Regresar</a>
      </div>
    </div>
    <hr>
    @php
      $dist = \App\distribuciones::whereRAW("md5(id)='".Request::get("cid")."'")->first();
    @endphp
        @csrf
        <div class="row">
          <div class="col">
            <input type="hidden" name="dist_id" value="{{$dist->id}}">
            <label for="">Concepto:</label>
            <input class="form-control enable allow" required type="text" name="concepto" placeholder="Ej: Costo Unisant">
            <label for="">Tipo:</label>
            <select class="form-control allow selecta" required name="tipo">
              <option value="">Seleccionar</option>
              <option value="Porcentaje sobre utilidad">Porcentaje despues de utilidad</option>
              <option value="Monto fijo por">Antes de utilidad</option>
              <option value="Monto sobre utilidad">Monto despues de utilidad</option>
              <option value="Monto sí hay pasarela">Pasarela</option>
            </select>
            <label for="">Opcional:</label>
            <select class="form-control allow" required name="opcional">
              <option value="">Seleccionar</option>
              <option value="0">Obligatorio</option>
              <option value="1">Opcional</option>
            </select>
            <div class="conceptopago d-none">
              <label for="">Concepto de pago:</label>
              <select class="form-control allow" name="conceptopago">
                <option value="">Seleccionar</option>
                @foreach (\App\conceptospago::all() as $concepto)
                  <option value="{{$concepto->id}}">{{$concepto->concepto}}</option>
                @endforeach
              </select>
            </div>
            <label for="">Cantidad del <span class='what'></span>:</label>
            <input class="form-control enable allow" step="0.01" required type="text" name="cantidad" placeholder="Ej: 56">
          </div>
        </div>
      <hr>
      <a href="/pagos/distribuir?cid={{Request::get("cid")}}" class="btn btn-danger">
        <i class="fas fa-ban"></i>
        Regresar
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
