@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
    $acc = \Auth::user()->accesos;
    $accesos = [];
    $i = 0;
    foreach ($acc as $ac) {
      $accesos[$i++] = $ac->sede->id;
    }

    $conciliaciones = \App\conciliaciones::wherehas("sede",function($query) use ($accesos){
      $query->whereIn("sede_id",$accesos);
    })->where("estado",1)->orderby("created_at","desc");
    
  @endphp
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h3>Historial de conciliaciones</h3>
          <hr>
          <table class="table facturas">
            <thead class=" text-primary">
              <tr>
                <th>
                  Folio
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
                  Materias
                </th>
                <th>
                  $ Materias
                </th>
                <th>
                  $ Requerimientos
                </th>
                <th>Estado</th>
                <th>Total</th>
                <th>Acciones</th>
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
                  <td>
                    {{\Carbon\Carbon::parse($c->created_at)->format("Ym")}}{{$c->id}}
                  </td>
                  <td>{{$c->sede->sede}}</td>
                  <td>
                    <b>
                      {{\Carbon\Carbon::parse($c->created_at)->format("Y-M-d")}}
                    </b>
                  </td>
                  <td>{{count($c->pagos)}}</td>
                  <td>{{count($c->requerimientos)}}</td>
                  <td>{{$amount->format($total_p)}}</td>
                  <td>{{$amount->format($total_r)}}</td>
                  <td>{{$c->estado == null ? "En proceso" : "Concluido"}}</td>
                  <td>{{$amount->format($total_r+$total_p)}}</td>
                  <td>
                    <a href="/conciliaciones/requerimientos?cid={{md5($c->id)}}">
                      Detalles de la conciliación
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('scripts')
  <script type="text/javascript">
      $(".facturas").DataTable(lang);
  </script>
@endsection
