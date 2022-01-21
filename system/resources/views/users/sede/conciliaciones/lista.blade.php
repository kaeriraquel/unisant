@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Todas las conciliaciones</h4>
    <p class="card-category">Conciliaciones en proceso y concluidas</p>
  </div>
  <div class="card-body">
    <div class="">
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
          @php
          $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
          @endphp
          @foreach (Auth::user()->sede->sede->conciliaciones as $c)
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
                <a href="/conciliaciones/requerimientos?cid={{md5($c->id)}}">
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
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".facturas").DataTable(lang);
  </script>
@endsection
