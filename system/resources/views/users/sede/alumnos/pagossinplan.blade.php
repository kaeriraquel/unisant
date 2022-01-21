@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header">
    <h4 class="card-title ">Alumnos estatales</h4>
    <p class="card-category">Con pagos sin plan asignado</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">

      </div>
    </div>
      <table class="table alumnos" data-page-length='200'>
        <thead class=" text-primary">
          <tr>
            <th>
              Fecha de pago
            </th>
            <th>
              Monto
            </th>
            <th>
              Matricula
            </th>
            <th>
              Sede
            </th>
          </tr>
        </thead>
        <tbody>
          @php
            $data =  \App\pagos::doesntHave("plan_pagos")
            ->where("sede_id",\Auth::user()->sede->sede->id)
            ->where("deleted_at",NULL)
            ->where("returned_at",NULL)
            ->get();
            $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
          @endphp
          @foreach ($data as $al)
              <tr>
                <td>
                  {{\Carbon\carbon::parse($al->fecha_pago)->format("Y-m-d")}}
                </td>
                <td>
                  {{$amount->format($al->monto)}}
                </td>
                <td>
                  <a href="/alumnos/pagos?cid={{base64_encode($al->matricula)}}">
                    {{$al->matricula}}
                  </a>
                </td>
                <td>
                  {{$al->sedex->sede}}
                </td>
              </tr>
          @endforeach
        </tbody>
      </table>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".alumnos").DataTable(lang);
  </script>
@endsection
