@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Lista de solicitudes pendientes</h3>
      </div>
      <div class="float-right">

      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-12 text-right">

      </div>
    </div>
    @php
      $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
      $bajas = \App\alumnosest::where("baja",1)->get();
      $devoluciones = \App\pagos::where("estado",10)->where("returned_at","<>",NULL)->get();
    @endphp
    @if (count($bajas) > 0)
      <h4>Bajas</h4>
      <table class="table alumnos">
        <thead class=" text-primary">
          <tr>
            <th>
              Estado
            </th>
            <th>
              Estado
            </th>
            <th>
              Matricula
            </th>
            <th>
              Nombre
            </th>
            <th>
              Autorizar
            </th>
            <th>
              Grupo
            </th>
            <th>
              Sede
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach ($bajas as $al)
              <tr>
                <td>
                  <div class="text-{{$al->baja == NULL ? 'success' : ($al->baja == 1 ? "warning" : "danger")}}">
                    <span class="material-icons">
                    trip_origin
                    </span>
                  </div>
                </td>
                <td>{{$al->baja == NULL ? 'Activo' : ($al->baja == 1 ? "En proceso de baja" : "Baja")}}</td>
                <td>
                  {{$al->matricula}}
                </td>
                <td>
                    {{$al->nombre_completo}}
                </td>
                <td>
                  <form action="/alumnos/darbaja" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{base64_encode($al->matricula)}}">
                    <button class="btn btn-danger" type="submit">
                      <i class="fas fa-hand-paper"></i> Autorizar
                    </button>
                  </form>
                  <form action="/alumnos/cancelar" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{base64_encode($al->matricula)}}">
                    <button class="btn btn-success" type="submit">
                      <i class="fas fa-ban"></i> Cancelar
                    </button>
                  </form>
                </td>
                <td>
                  {{$al->grupo ? $al->grupo->grupo : "Sin grupo"}}
                </td>
                <td>
                  {{$al->sede->sede}}
                </td>
              </tr>
          @endforeach
        </tbody>
      </table>
    @endif
    @if (count($devoluciones) > 0)
      <h4>Pagos a devolver</h4>
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
            <th>Autorizar</th>
            <th>Denegar</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($devoluciones as $pago)
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
                <td>
                  <form action="/pagos/autorizardev" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{md5($pago->id)}}">
                    <button class="btn-normal" type="submit">
                      <i class="fas fa-hand-paper"></i> Autorizar
                    </button>
                  </form>
                </td>
                <td>
                  <form action="/pagos/cancelardev" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{md5($pago->id)}}">
                    <button class="btn-normal" type="submit">
                      <i class="fas fa-ban"></i> Cancelar
                    </button>
                  </form>
                </td>
              </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <a class="btn btn-link text-info" href="/alumnos/bajas">Historial de bajas</a>
            <a class="btn btn-link text-info" href="/alumnos/devoluciones">Historial de devoluciones</a>
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
