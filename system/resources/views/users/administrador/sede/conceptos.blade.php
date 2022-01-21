@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
@php
  $sede = \App\sedes::whereRAW("md5(id)='".Request::get('cid')."'")->first();
  $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
  $conceptos = $sede->conceptos;
  $conceptos_id = [];
  $i = 0;
  foreach ($conceptos as $concepto_sede) {
    $conceptos_id[$i++] = $concepto_sede->concepto_id;
  }
@endphp
<div class="card">
  <div class="card-body">
    <h3>Añadir conceptos de pago a <a href="/sede/lista">sede</a></h3>
    <hr>
    <form class="input" action="/sedes/addconcepto" method="post">
      @csrf
      <div class="row">
        <div class="col-4">
          <label for="">Selecciona el concepto:</label>
          <input type="hidden" name="cid" value="{{$sede->id}}">
          <select style="padding-left:10px;" class="form-control allow" required name="concepto_id">
            <option value="">Seleccione concepto</option>
            @foreach (\App\conceptospago::whereNotIn("id",$conceptos_id)->get() as $con)
              <option value="{{$con->id}}">{{$con->concepto}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-4">
          <br>
          <input class="btn btn-primary" type="submit" name="" value="Añadir">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <h3>Conceptos de pago {{$sede->sede}}</h3>
    <hr>
    <div class="row">
      @php
        $sede = \App\sedes::whereRAW("md5(id)='".Request::get("cid")."'")->first();
      @endphp
    </div>
      @if(count($sede->conceptos) > 0)
        <table class="table table1">
          <thead>
            <th>Folio</th>
            <th>Concepto</th>
            <th></th>
          </thead>
          <tbody>
            @foreach ($sede->conceptos as $concepto_sede)
              @php
                $con = $concepto_sede->concepto;
              @endphp
              @if (isset($con))
                <tr>
                  <td>{{\Carbon\Carbon::parse($con->created_at)->format("Y")}}{{$con->id}}</td>
                  <td>{{$con->concepto}}</td>
                  <td>
                    <form action="/sedes/delconcepto" method="post">
                      @csrf
                      <input type="hidden" name="id" value="{{$concepto_sede->id}}">
                      <button type="submit" class="btn btn-link">
                        <i class="fa fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @else
                  <tr>
                    <td>NULL</td>
                    <td>NULL este concepto fue eliminado</td>
                    <td>
                      <form action="/sedes/delconcepto" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$concepto_sede->id}}">
                        <button type="submit" class="btn btn-link">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      @else
      <h3 class="text-center">
        <i class="fas fa-exclamation-triangle text-warning"></i> No hay conceptos de pago asociados
      </h3>
    @endif
  </div>
</div>
<div class="card">
  <div class="card-body">
    <form class="input" action="/sedes/copyconceptofrom" method="post">
      @csrf
      <div class="row">
        <div class="col-4">
          <label for="">Copiar desde la sede:</label>
          <input type="hidden" name="cid" value="{{$sede->id}}">
          <select style="padding-left:10px;" class="form-control allow" required name="sede_id">
            <option value="">Seleccione sede</option>
            @foreach (\App\sedes::all() as $sed)
              <option value="{{$sed->id}}">{{$sed->sede}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-4">
          <br>
          <input class="btn btn-primary" type="submit" name="" value="Copiar">
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table1").DataTable(lang);
  </script>
@endsection
