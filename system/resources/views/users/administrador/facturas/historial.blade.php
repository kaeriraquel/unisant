@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Solicitud de facturas</h4>
    <p class="card-category">Alumnos solicitando facturas de pago</p>
  </div>
  <div class="card-body">
    <div class="">
      <table class="table facturas">
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Monto
            </th>
            <th>
              Detalles
            </th>
            <th>
              Solicitud
            </th>
            <th class="text-right">
              Acciones
            </th>
        </tr>
      </thead>
        <tbody>
          @php
            $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
          @endphp
            @foreach (\App\facturas::orderBy("status","DESC")->where("status",1)->get() as $fact)
              @if ($fact->pago != null)
                <tr>
                  <td>
                    {{\Carbon\Carbon::parse($fact->pago->created_at)->format("Ym")}}{{$fact->pago->id}}
                  </td>
                  <td>
                    {{$amount->format($fact->pago->monto)}}
                  </td>
                  <td>
                    <a href="/alumnos/pagos?cid={{base64_encode($fact->pago->matricula)}}#facturacion">
                      Detalles del alumno
                    </a>
                  </td>
                  <td>
                    <a target="_blank" href="/invoice/{{\Carbon\Carbon::parse($fact->pago->created_at)->format("Ym")}}{{$fact->pago->id}}">
                      Ver solicitud
                    </a>
                  </td>
                  <td class="td-actions text-right">
                    @if ($fact->status == null)
                      <form action="/facturas/check?cid={{md5($fact->id)}}" method="post">
                        <button type="submit" class="btn btn-success btn-link" href="/facturas/check?cid={{md5($fact->id)}}">
                          <span class="material-icons">
                          task_alt
                          </span> Marcar como entregada
                          <div class="ripple-container"></div>
                        </button>
                        @csrf
                      </form>
                      @else
                        <form action="/facturas/uncheck?cid={{md5($fact->id)}}" method="post">
                          <button type="submit" rel="tooltip" class="btn btn-danger btn-link">
                            <span class="material-icons">
                            radio_button_unchecked
                            </span> Marcar como no entregada
                            <div class="ripple-container"></div>
                          </button>
                          @csrf
                        </form>
                    @endif
                  </tr>
                @else
                  @php
                    $fact->delete();
                  @endphp
              @endif
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
