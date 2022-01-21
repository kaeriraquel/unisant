@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
    $conciliaciones = \App\conciliaciones::where("estado",null)->orderby("created_at","desc");
    $chistorial = \App\conciliaciones::where("estado",1);
    $facturas = \App\facturas::where("status",null);
  @endphp
  <div class="row">
    <div class="col">
      <div class="card text-center">
        <h3 class="text-success">
          {{$conciliaciones->count()}}
        </h3>
        <h6>Conciliaciones pendientes</h6>
      </div>
    </div>
    <div class="col">
      <div class="card text-center">
        <h3 class="text-success">
          {{$facturas->count()}}
        </h3>
        <h6>Facturas pendientes</h6>
      </div>
    </div>
    <div class="col">
      <div class="card text-center">
        <h3 class="text-success">
          {{$chistorial->count()}}
        </h3>
        <h6>Conciliaciones realizadas</h6>
      </div>
    </div>
    <div class="col"></div>
    <div class="col"></div>
    <div class="col"></div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h3>Conciliaciones pendientes</h3>
          <hr>
          <table class="table facturas" data-page-length="100">
            <thead class=" text-primary">
              <tr>
                <th>
                  Folio
                </th>
                <th>
                  Concepto
                </th>
                <th>
                  Sede
                </th>
                <th>
                  Conciliación
                </th>
                <th>
                  Pagos
                </th>
                <th>
                  Monto pagos
                </th>
                <th>
                  Reqs
                </th>
                <th>
                  Monto req
                </th>
                <th>
                  Estado
                </th>
            </tr>
          </thead>
            <tbody>
              @foreach ($conciliaciones->get() as $c)
                @php
                  $total_p = 0;
                  $total_r = 0;
                  foreach ($c->pagos as $_p) {
                    $total_p += $_p->monto;
                  }
                  foreach ($c->requerimientos as $_r) {
                    $total_r += $_r->monto;
                  }
                @endphp
                <tr>
                  <td>{{\Carbon\carbon::parse($c->created_at)->format("Ym")}}{{$c->id}}</td>
                  <td>
                    <a class="con" href="#" w="/conciliaciones/requerimientos?cid={{md5($c->id)}}">
                      {{$c->concepto ?: "Sin concepto"}}
                    </a>
                  </td>
                  <td>{{$c->sede->sede}}</td>
                  <td>
                    <b>
                      {{\Carbon\Carbon::parse($c->created_at)->format("Y-m-d")}}
                    </b>
                  </td>
                  <td>
                    {{count($c->pagos)}}
                  </td>
                  <td>
                      <span class="text-light badge rounded-pill bg-success">
                        {{$amount->format($total_p)}}
                      </span>
                  </td>
                  <td>
                    {{count($c->requerimientos)}}
                  </td>
                  <td>
                    <span class="text-light badge rounded-pill bg-danger">
                      {{$amount->format($total_r)}}
                    </span>
                  </td>
                  <td>{{$c->estado == null ? "En proceso" : "Concluido"}}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <a class="btn btn-link text-info" href="/conciliaciones/historial">Historial de conciliaciones</a>
          <a class="btn btn-link text-info" href="/facturas/historial">Historial de facturas</a>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".con").bind("click",function(){
        event.preventDefault();
        ShowWaitNotifyTime("Generando conciliación, esto puede tardar la primera vez.")
        location.href = $(this).attr("w");
      });
      $(".facturas").DataTable(lang);
  </script>
@endsection
