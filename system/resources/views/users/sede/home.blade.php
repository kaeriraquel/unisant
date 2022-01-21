@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])
@php
  $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
  $ps = \App\pagos::whereHas("sede",function($q){
    $q->where("sede_id",auth()->user()->sede->sede->id);
  })->where("estado",NULL)->where("deleted_at",NULL)->where("returned_at",NULL)->get();
@endphp
@section('content2')
<div class="row">
  <div class="col">
    <div class="card text-center">
      <h3 class="text-success">
        {{$ps->count()}}
      </h3>
      <h6>Pagos por conciliar</h6>
    </div>
  </div>
  <div class="col">
    <div class="card text-center">
      <h3 class="text-success">
        @php
          $total = 0;
          foreach($ps as $pago){
            $total += $pago->monto;
          }

        @endphp
        {{$amount->format($total)}}
      </h3>
      <h6>Total en espera</h6>
    </div>
  </div>
  <div class="col">
    <div class="card text-center">
      <h3 class="text-success">
        {{count(\Auth::user()->sede->sede->revoes)}}
      </h3>
      <h6>RV estatales</h6>
    </div>
  </div>
  <div class="col">
    <div class="card text-center">
      <h3 class="text-success">
        {{Auth::user()->sede->sede->count_alumnos}}
      </h3>
      <h6>Alumnos federales</h6>
    </div>
  </div>
  <div class="col">
    <div class="card text-center">
      <h3 class="text-success">
        {{count(\Auth::user()->sede->sede->alumnos)}}
      </h3>
      <h6>Alumnos estatales</h6>
    </div>
  </div>

</div>
<div class="row">
  <div class="col">
    <a href="/alumnos/noplan">
      <div class="card text-center">
        <h3 class="text-success">
          {{\App\alumnosest::where("sede_id",\Auth::user()->sede->sede->id)->doesntHave("planespago")->count()
            + \App\nombres::where("sede_id",\Auth::user()->sede->sede->id)->doesntHave("planespago")->count()
          }}
        </h3>
        <h6>Alumnos sin plan</h6>
      </div>
    </a>
  </div>
  <div class="col">
    <a href="/alumnos/nofecha">
      <div class="card text-center">
        <h3 class="text-success">
          {{\App\alumnosest::where("sede_id",\Auth::user()->sede->sede->id)->whereHas("planespago",function($query){
            $query->where("every",NULL)->where("since",NULL);
          })->count()
          +
          \App\nombres::where("sede_id",\Auth::user()->sede->sede->id)->whereHas("planespago",function($query){
            $query->where("every",NULL)->where("since",NULL);
          })->count()}}
        </h3>
        <h6>Planes sin fechas</h6>
      </div>
    </a>
  </div>

  <div class="col">
    <a href="/alumnos/pagossinplan">
      <div class="card text-center">
        <h3 class="text-success">
          {{\App\pagos::doesntHave("plan_pagos")
          ->where("sede_id",\Auth::user()->sede->sede->id)
          ->where("deleted_at",NULL)
          ->where("returned_at",NULL)
          ->count()}}
        </h3>
        <h6>Pagos sin plan</h6>
      </div>
    </a>
  </div>

  <div class="col">
    <a href="/alumnos/pagosatrasados">
      <div class="card text-center">
        <h3 class="text-success">
          {{\Auth::user()->sede->sede->deudas ?: "0"}}
        </h3>
        <h6>Planes atrasados</h6>
      </div>
    </a>
  </div>

</div>
<div class="row">

  <div class="col-6">
    <div class="card text-center">
        @php
          $total_p = 0;
          $total_r = 0;
          $c3 = 0;
          foreach (\Auth::user()->sede->sede->conciliaciones as $c) {
            if($c->estado == null){
              foreach ($c->pagos as $_p) {
                $total_p += $_p->monto;
              }
              foreach ($c->requerimientos as $_r) {
                $total_r += $_r->monto;
              }
              $c3++;

            }
          }
        @endphp
        <div class="row">
          <div class="col-12">
            <br>
            <div class="row">
              <div class="col">
                <h4 class="text-success">
                  {{$c3}}
                </h4>
                <h6>Conciliaciones en proceso</h6>
              </div>
              <div class="col">
                <h4 class="text-success">
                  {{$amount->format($total_p)}}
                </h4>
                <h6>Pagos</h6>
              </div>
              <div class="col">
                <h4 class="text-danger">
                  {{$amount->format($total_r)}}
                </h4>
                <h6>Requerimientos</h6>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
  <div class="col-6">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <a class=" btn-block btn btn-primary" href="/sede/conciliaciones/lista">
              <i class="fas fa-clipboard-list"></i> Todas las concialiaciones
            </a>
          </div>
          <div class="col">
            <a class="btn-block btn btn-success" href="/conciliaciones/nueva">
              <i class="fas fa-plus"></i>
              Solicitar conciliación de pago</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="card">
      <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h3>Pagos por conciliar</h3>
            </div>
            <div class="float-right">

            </div>
          </div>
          <hr>
          <table data-page-length="50" class="table pagos table-striped">
            <thead>
              <th>Folio</th>
              <th>Monto</th>
              <th>Pagos</th>
              <th>Cargos</th>
              <th>Sede</th>
              <th>Nombre</th>
              <th>Fecha de pago</th>
              <th>Comprobante</th>
              <th>Tipo de pago</th>
              <th>Estado</th>
            </thead>
            <tbody>
              @php
                $total_pagado = 0;
              @endphp
              @foreach ($ps as $pago)
                @php
                  $total_pagado += $pago->monto;
                  $sede = (isset(Auth::user()->sede)) ? Auth::user()->sede->sede->sede : "Administrador";

                  $alumno = \App\alumnosest::where("matricula",$pago->matricula)->first();
                  $nombre = \App\nombres::where("matricula",$pago->matricula)->first();
                  if($alumno != null){
                    $al = (object) [];
                    $al->clave_alumno = $alumno->matricula;
                    $al->nombre = $alumno->nombre_completo;
                    $al->primer_apellido = "";
                    $al->segundo_apellido = "";
                    $al->materias_cursadas  = [];
                    $al->carrera = "";
                    $al->estado_alumno = "";
                    /// Cal Cal
                    $apr = 0;
                    $rep = 0;
                    $t = 0;

                    if($nombre == null){
                      $nombre = \App\nombres::create([
                        "matricula"=>$pago->matricula,
                        "sede_id"=>Auth::user()->sede->sede->id,
                        "nombre"=>$al->nombre." ".$al->primer_apellido." ".$al->segundo_apellido
                      ]);
                    }
                    if($nombre != NULL && $nombre->sede_id == NULL){
                      $nombre->update(["sede_id"=>Auth::user()->sede->sede->id]);
                    }
                  } else {
                    if($nombre == null)
                    {
                      $url = \App\sedes::where("sede",$sede)->first()->individual."&matricula=$pago->matricula";
                      $ch = curl_init($url);
                      curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
                      curl_setopt($ch, CURLOPT_HEADER, 0);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                      $res = curl_exec($ch);
                      curl_close($ch);
                      $data = json_decode($res,true);
                      if(isset($data["response"])){
                        $al = (object) $data["response"];
                        $nombre = \App\nombres::create([
                          "matricula"=>$pago->matricula,
                          "nombre"=>$al->nombre." ".$al->primer_apellido." ".$al->segundo_apellido
                        ]);
                        $t = count($al->materias_cursadas);
                        $rep = 0;
                        foreach($al->materias_cursadas as $m){
                          $m = (object) $m;
                          if(floatval($m->calificacion) < 7)
                            $rep++;
                        }
                        $apr = count($al->materias_cursadas) - $rep;
                      } else {
                        $nombre = (object) ["nombre"=>"Sin nombre"];
                      }
                    }
                  }
                @endphp
                <tr>
                  <td>
                    {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                  </td>
                  <td>
                    {{$amount->format($pago->monto)}}
                  </td>
                  <td>
                      {{$pago->cantidad_pagos == NULL ? "1" : $pago->cantidad_pagos}}
                  </td>
                  <td>
                    @if ($pago->extra == NULL)
                      Sin cargos
                      @else
                        <div class="fw-bold text-{{$pago->extra > 0 ? "success" : "danger"}}">
                          {{$pago->metodo.$pago->extra}}
                        </div>
                    @endif
                  </td>
                  <td>
                    {{isset($pago->sede) ? $pago->sede->sede->sede : "Sin sede"}}
                  </td>
                  <td>
                    <a class="text-danger" href="/alumnos/pagos?cid={{base64_encode($pago->matricula)}}">
                      {{$nombre->alumno? $nombre->alumno->nombre_completo." ".$nombre->alumno->apat." ".$nombre->alumno->amat : "$nombre->nombre"}}
                    </a>
                  </td>
                  <td>
                    {{$pago->created_at}}
                  </td>
                  <td>
                    {!!($pago->document_id == 0 ? "Sin comprobante" : "<a target='_blank' href='/ver/".md5($pago->document_id)."'>Ver comprobante</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>Descargar</a>")!!}
                  </td>
                  <td>
                    {{$pago->clave}}
                  </td>
                  <td>
                    @if ($pago->estado == null)
                        En revisión
                      @else
                        @if ($pago->estado == 1)
                            Pendiente
                          @else
                            Distribuida
                        @endif
                    @endif
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
      $(".pagos").DataTable(lang);
  </script>
@endsection
