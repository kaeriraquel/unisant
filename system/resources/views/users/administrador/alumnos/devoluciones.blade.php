@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Solicitudes archivadas</h4>
    <p class="card-category">Pagos devueltos</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">

      </div>
    </div>
    <table class="table alumnos">
      <thead class=" text-primary">
        <tr>
          <th>Folio</th>
          <th>Estado</th>
          <th>Monto</th>
          <th>Alumno</th>
          <th>Grupo</th>
          <th>Fecha de pago</th>
          <th>Comprobante</th>
          <th>Tipo de pago</th>
          <th>Concepto</th>
        </tr>
      </thead>
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
      @endphp
      <tbody>
        @foreach (\App\pagos::where("returned_at","<>",NULL)->whereIn("estado",[NULL,1])->get() as $pago)
            <tr>
              <td>
                {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
              </td>
              <td>
                @if ($pago->returned_at != NULL)
                  <i class="fas fa-undo-alt text-danger"></i>
                @elseif ($pago->deleted_at != NULL)
                  <i class="fas fa-eraser"></i>
                @else
                  <i class="fas fa-check-circle"></i>
                @endif
              </td>
              <td>
                {{$amount->format($pago->monto)}}
              </td>
              <td>
                @php
                  $nombre = \App\nombres::where("matricula",$pago->matricula)->first();
                @endphp
                <a class="text-danger" href="/alumnos/pagos?cid={{base64_encode($pago->matricula)}}&did={{$pago->sede->sede_id}}&pago={{md5($pago->id)}}">
                  @if (Request::has("pago") && Request::get("pago") == md5($pago->id))
                    <i class="fas fa-arrow-circle-right text-success"></i>
                  @endif
                  @if (session()->has("debug"))
                    {{$pago->matricula}}
                    @else
                      @if(isset($nombre))
                        {{$nombre->nombre}}
                      @else
                        @php
                          $a = \App\alumnosest::where("matricula",$pago->matricula)->first();
                        @endphp
                        @if ($a != null)
                            {{$a->nombre_completo." ".$a->apat." ".$a->amat}}
                          @else
                            Sin nombre ({{$pago->matricula}})
                        @endif
                      @endif
                  @endif
                </a>
              </td>
              <td>
                {{\App\grupos::where("matricula",$pago->matricula)->first()->grupo}}
              </td>
              <td>
                {{\Carbon\carbon::parse(($pago->fecha_pago ?: NULL))->format("Y-M-d")}}
              </td>
              <td>
                {!!($pago->document_id == 0 ?
                   "Sin comprobante" :
                    "<a target='_blank' href='/ver/".md5($pago->document_id)."'>".'<i class="fas fa-image"></i>'."</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>".'<i class="fas fa-download"></i>'."</a>")!!}
              </td>
              <td>
                {{$pago->clave}}
              </td>
              <td>
                {{$pago->concepto ?: "Sin concepto"}}
              </td>
            </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <a class="btn btn-link text-info" href="/alumnos/bajas">Historial de bajas</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".alumnos").DataTable(lang);
  </script>
@endsection
