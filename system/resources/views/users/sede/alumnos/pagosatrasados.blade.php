@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h4>Pagos retrasados</h4>
    <hr>
    <table class="alumnos table table-striped">
      <thead>
        <th>Matrícula</th>
        <th>Nombre</th>
        <th>Fecha de pago</th>
        <th>Número de pago</th>
        <th>Monto</th>
      </thead>
      <tbody>
        @php
          $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
          $sede_id = \Auth::user()->sede->sede->id;
          $deudas = 0;

          $planes_federales = \App\alumnos_planes::whereHas("nombres",function($query) use($sede_id){
            $query->where("sede_id",$sede_id);
          })->where("every","<>",NULL)->where("since","<>",NULL)->where("disable",NULL)->get();

          $planes_estatales = \App\alumnos_planes::whereHas("alumno",function($query) use($sede_id){
            $query->where("sede_id",$sede_id);
          })->where("every","<>",NULL)->where("since","<>",NULL)->where("disable",NULL)->get();

          $planes_sede = $planes_federales->merge($planes_estatales);

          foreach ($planes_sede as $plan) {

              $monto_recaudado = 0;
              $since = $plan->since;
              $every = $plan->every;
              $desde = \Carbon\carbon::parse($since);

              if($since != NULL && $every != NULL){
                $_fechapago = null;
                if($every <= 30){
                  $desde->subDays($every);
                } elseif($every == 31){
                  $desde->subMonth(1);
                }
              }

              foreach ($plan->planes_pagos as $pp) {
                if($pp->pago != null){
                    $monto_recaudado += $pp->pago->monto;
                  }
                }


              for ($o = 1; $o <= $plan->plan->plazo;$o++){
                $success = "bg-success";
                $abonado = "bg-warning";
                $none = "d-none";
                $monto_ = $plan->plan->monto/$plan->plan->plazo;
                $monto = $plan->plan->monto/$plan->plan->plazo;
                $status = $none;

                if($monto <= $monto_recaudado){
                  $status = $success;
                  $monto_recaudado -= $monto;
                } elseif(intval($monto_recaudado) == 0){
                  $status = $none;
                  $monto_recaudado = 0;
                } elseif($monto_recaudado > 0) {
                   $status = $abonado;
                   $monto_recaudado = 0;
                }

                if($since != NULL && $every != NULL){
                  $_fechapago = null;
                  if($every <= 30){
                    $desde->addDays($every);
                  } elseif($every == 31){
                    $desde->addMonth(1);
                  }
                }

                if($status == $none && $desde->lt(\Carbon\carbon::now())){
                  $nombre = \App\nombres::where("matricula",$plan->matricula)->first();
                  if($nombre == NULL){
                    $nombre = \App\alumnosest::where("matricula",$plan->matricula)->first();
                    if($nombre == NULL)
                    {
                      $nombre = "Sin nombre";
                    } else {
                      $nombre = $nombre->nombre_completo." ". $nombre->apat." ".$nombre->amat;
                    }
                  } else {
                    $nombre = $nombre->nombre;
                  }
                  echo "<tr>";
                    echo "<td>";
                    echo $plan->matricula;
                    echo "</td>";
                    echo "<td>";
                    echo "<a href='/alumnos/planes?cid=".base64_encode($plan->matricula)."'>$nombre</a>";
                    echo "</td>";
                    echo "<td>";
                    echo $desde->format("Y-m-d");
                    echo "</td>";
                    echo "<td>";
                    echo $o;
                    echo "</td>";
                    echo "<td>";
                    echo $amount->format($monto_);
                    echo "</td>";
                  echo "</tr>";
                  $deudas++;
                }
              }
            }

            \Auth::user()->sede->sede->update(["deudas"=>$deudas]);
        @endphp
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
