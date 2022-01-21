@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
@php
  $sede = \App\sedes::whereRAW("md5(id)='".Request::get('cid')."'")->first();
  $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
  $grupos = \App\alumnosest::where("sede_id",$sede->id)->get();
  $grupos2 = [];
  foreach ($grupos as $gp) {
    if(!isset($grupos2[$gp->grupo->grupo])){
      $grupos2[$gp->grupo->grupo] = 0;
    }
    $grupos2[$gp->grupo->grupo]++;
  }
  $url = $sede->todos;

  $ch = curl_init($url);

  curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($ch);
  curl_close($ch);
  $data = json_decode($res,true);
  $counter = 0;

  $gruposfed = [];
  if(isset($data["response"])){
    foreach ($data["response"] as $a){
      $al = (object) $a;
      $grupo = \App\grupos::where("matricula",$al->clave_alumno)->first();
      if($grupo == null){
        $grupo = \App\grupos::create(["alumnoest_id"=>0,"grupo"=>"Sin grupo","matricula"=>$al->clave_alumno]);
      }
      if(isset($gruposfed[$grupo->grupo])){
        $gruposfed[$grupo->grupo]++;
      } else {
        $gruposfed[$grupo->grupo] = 1;
      }
    }
  }

@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Grupos de {{$sede->sede}}</h3>

        <p>
          Lista que muestra todos los grupos de los alumnos por cada una de las sedes y si poseen respectivas distribuciones.
        </p>
      </div>
      <div class="float-right">
        <br>
        <a href="/sede/lista">Regresar</a>
      </div>
    </div>
    <hr>
      @if(count($grupos) > 0)
        <table class="table table1">
          <thead>
            <th>Grupo</th>
            <th>Total</th>
            <th>Tiene distribución</th>
          </thead>
          <tbody>
            @foreach ($gruposfed as $grupo => $val)
              @php
                $dist = \App\dist_grupos::where("grupo",$grupo)->first();
              @endphp
              <tr>
                <td>{{$grupo}}</td>
                <td>
                  {{$val}}
                </td>
                <td>
                  @if ($dist != NULL)
                      <i class="fas fa-check-circle text-success"></i>
                    @else
                      <i class="fas fa-ban text-danger"></i>
                  @endif
                </td>
              </tr>
            @endforeach
            @foreach ($grupos2 as $al => $grupo)
              @php
                $dist = \App\dist_grupos::whereRAW("replace(grupo,' ','')='".str_replace(" ","",$al)."'")->first();
              @endphp
              <tr>
                <td>{{$al}}</td>
                <td>
                  {{$grupo}}
                </td>
                <td>
                  @if ($dist != NULL)
                      <i class="fas fa-check-circle text-success"></i>
                    @else
                      <i class="fas fa-ban text-danger"></i>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
      <h3 class="text-center">
        @if(!isset($data["response"]))
            <i class="fas fa-exclamation-triangle text-danger"></i> REVISE LAS COMUNICACIONES API DE ESTA SEDE
          @else
            <i class="fas fa-exclamation-triangle text-warning"></i> No hay grupos para esta sede
        @endif

      </h3>
    @endif
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table1").DataTable(lang);
  </script>
@endsection
