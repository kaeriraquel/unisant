@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <form class="form" action="/pagos/nuevapasarela" method="post">
@php
  $menu = ["Regresar" => "/pagos/pasarelas"];
@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Nueva pasarela</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="/pagos/pasarelas">Regresar</a>
      </div>
    </div>
    <hr>
        @csrf
        <div class="row">
          <div class="col">
            <label class="text-dark" for="">Nombre:</label>
            <input class="form-control enable allow" required type="text" name="name" placeholder="Paypal">
            <label class="text-dark" for="">Comisión en %:</label>
            <input class="form-control enable allow" step="0.00" required type="text" name="comision" placeholder="2.29">
            <label class="text-dark" for="">Fijo:</label>
            <input class="form-control enable allow" step="0.00" required type="text" name="fijo" placeholder="2.5">
            <label class="text-dark" for="">¿Incluir IVA?:</label>
            <select class="form-control allow" required name="iva">
              <option value="">Seleccionar</option>
              <option value="1">SI</option>
              <option value="0">NO</option>
            </select>
            <label class="text-dark" for="">Concepto de pago:</label>
            <select class="form-control allow" name="forma_pago">
              <option value="">Seleccionar</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Efectivo">Efectivo</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
        </div>
      <hr>
      <a href="/pagos/pasarelas" class="btn btn-danger">
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
