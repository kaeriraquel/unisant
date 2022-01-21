@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Alumnos</h4>
    <p class="card-category">Todos los alumnos de mi sede</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">

      </div>
    </div>
    @php
      $url = \App\sedes::where("sede",Auth::user()->sede->sede->sede)->first()->todos;

      $ch = curl_init($url);

      curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $res = curl_exec($ch);
      curl_close($ch);
      $data = json_decode($res,true);
      $counter = 0;
    @endphp
    @if (isset($data["response"]))
      <table class="table alumnos">
        <thead class=" text-primary">
          <tr>
            <th>
              Alta
            </th>
            <th>
              Mat
            </th>
            <th>
              Nombre
            </th>
            <th>
              Materias cursadas
            </th>
            <th>Grupo</th>
            <th>
              Aprobadas
            </th>
            <th>
              Reprobadas
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data["response"] as $a)
            @php
            $counter++;
              $al = (object) $a;
              $nombre = \App\nombres::where("matricula",$al->clave_alumno)->first();
              if($nombre == null)
              {
                $nombre = \App\nombres::create([
                  "matricula"=>$al->clave_alumno,
                  "nombre"=>$al->nombre." ".$al->primer_apellido." ".$al->segundo_apellido
                ]);
              }
            @endphp
            @if (1)
              <tr>
                <td>
                  <div class="text-{{$al->estado_alumno=='Activo' ? 'success' : "danger"}}">
                    {{strtoupper($al->estado_alumno)}}
                  </div>
                </td>
                <td>
                  {{$al->clave_alumno}}
                </td>
                <td>
                  <a href="/alumnos/pagos?cid={{base64_encode($al->clave_alumno)}}">
                    {{$nombre->nombre}}
                  </a>
                </td>
                <td>
                  {{count($al->materias_cursadas)}}
                  @php
                    $t = count($al->materias_cursadas);
                  @endphp
                </td>
                <td>
                  @php
                  $grupo = \App\grupos::where("matricula",$al->clave_alumno)->first();
                  if($grupo == null){
                    $grupo = \App\grupos::create(["alumnoest_id"=>0,"grupo"=>"Sin grupo","matricula"=>$al->clave_alumno]);
                  }
                  @endphp
                  {{$grupo->grupo}}
                </td>
                <td class="text-success">
                  @php
                    $rep = 0;
                    foreach($al->materias_cursadas as $m){
                      $m = (object) $m;
                      if(floatval($m->calificacion) < 7)
                        $rep++;
                    }
                    $apr = $t - $rep;
                  @endphp
                  ({{$t==0 ?: $apr/$t*100}}%)
                </td>
                <td>
                  ({{$t==0 ?: $rep/$t*100}}%)
                </td>
              </tr>
            @endif
          @endforeach
          @php
            Auth::user()->sede->sede->count_alumnos = $counter;
            Auth::user()->sede->sede->save();
          @endphp
        </tbody>
      </table>
      @else
        La sede no ha sido configurada para usuario federales
      @endif
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".alumnos").DataTable(lang);
  </script>
@endsection
