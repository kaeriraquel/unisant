@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Planes de pago</h3>
    @if (!request()->has("disabled"))
        <a href="?disabled=true">Archivo de planes desactivados</a>
      @else
        <a href="?">Ocultar planes desactivados</a>
    @endif
    <hr>
    <div class="clearfix">
      <div class="float-left">
          Los planes de pago son elementos de apoyo que permiten establecer un cobro controlado de los recursos activos del alumno.
      </div>
      <div class="float-right">
        <a href="/pagos/nuevoplan" class="btn btn-primary">Nuevo plan</a>
      </div>
    </div>
    <hr>
    <div class="">
      <table class="table facturas table-striped"  data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Concepto
            </th>
            <th>
              Monto
            </th>
            <th>
              Plazos
            </th>
            <th>
              Pago
            </th>
            <th>
              Concepto relacionado
            </th>
            <th></th>
        </tr>
      </thead>
        <tbody>
          @php
            $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
            $what = !request()->has("disabled") ? null : 1;
          @endphp
          @foreach (\App\planespago::all() as $plan)
            @if ($plan->disable == $what)
              <tr>
                <td>{{\Carbon\Carbon::parse($plan->created_at)->format("Y")}}{{$plan->id}}</td>
                <td>{{$plan->concepto}}</td>
                <td>{{$amount->format($plan->monto)}}</td>
                <td>{{$plan->plazo}}</td>
                <td>{{$amount->format($plan->monto/$plan->plazo)}}</td>
                <td>{{(isset($plan->conceptopago) ? $plan->conceptopago->concepto : "Sin concepto")}}</td>
                <td>
                  <form action="/pagos/{{!$plan->disable ? "dis" : "en"}}ableplan" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{md5($plan->id)}}">
                    <button type="submit" class="button-switch">
                      <i class="text-{{$plan->disable ? "primary" : "success"}} fa-2x fas fa-toggle-{{!$plan->disable ? "on" : "off" }}"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">

      </div>
    </div>
  </div>
</div>
@endsection
@section('styles')
  <style media="screen">
    .button-switch{
      cursor:pointer;
      background:none;
      border:none;
      margin:0;
      padding:0;
    }
    .button-switch:focus i{
      color:orange !important;
    }
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table").DataTable(lang);
  </script>
@endsection
