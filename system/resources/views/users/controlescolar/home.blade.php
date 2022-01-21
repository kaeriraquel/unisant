@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])
@section('content2')
    <div class="row">
      <div class="col-6">
        <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h4>Buscar alumno</h4>
              <p>Buscar alumnos utilizando su matrícula, nombre parcial, curp.</p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col">
                <form action="/home" method="get">
                  <input style="text-transform:uppercase" type="text" name="buscar" value="{{Request::has("buscar") ? strtoupper(Request::get("buscar")) : ""}}" class="form-buscar" placeholder="Buscar ...">
                  <button type="submit" class="btn-buscar" name="button">
                    <i class="fas fa-search"></i>
                  </button>
                </form>
            </div>
          </div>
          @if (Request::has("buscar"))
            <hr>
            @php
              $mySedes = [auth()->user()->sede->sede->id];
              $names = auth()->user()->sede->sede->sede;
              $acc = \Auth::user()->accesos;
              foreach ($acc as $ac) {
                array_push($mySedes,$ac->sede->sede->id);
                $names .= $ac->sede->sede->sede;
              }
              $mySedesRAW = join(",",$mySedes);
              $resultados =  [];
              $search = strtolower(Request::get("buscar"));
              $alumnos = \App\alumnosest::whereRAW("sede_id in (?) and (lower(matricula) like '%$search%' or
              lower(apat) like '%$search%' or
              lower(amat) like '%$search%' or
              lower(nombre_completo) like '%$search%' or
              lower(curp) like '%$search%')
              ",$mySedesRAW)->get();

              $nombres = \App\nombres::whereRAW("sede_id in (?) and (lower(matricula) like '%$search%' or
              lower(nombre) like '%$search%')
              ",$mySedesRAW)->get();

              $resultados = $alumnos->merge($nombres);
            @endphp
            @if (count($resultados) > 0)
              <small>Buscando en {{count($mySedes)}} sedes ({{$names}}) "{{mb_strtoupper($search)}}":</small>
              <hr>
              <table class="table table-striped">
                @foreach ($resultados as $res)
                  @php
                    $nombre = isset($res->nombre) ? $res->nombre : "$res->nombre_completo $res->apat $res->amat";
                    $estado = isset($res->nombre) ? "Federal" : "Estatal";
                    $nombre = mb_strtoupper($nombre);
                  @endphp
                  <tr>
                    <td>
                      {{strtoupper($res->matricula)}}
                    </td>
                    <td>
                      <a href="/titulacion/view?cid={{base64_encode($res->matricula)}}">{{$nombre}}</a>
                    </td>
                    <td>
                      {{$estado}}
                    </td>
                    <td>
                      {{$res->sede ? $res->sede->sede : "No especificada"}}
                    </td>
                  </tr>
                @endforeach
              </table>

              @else
                <div class="text-center">
                  No hay resultados para "{{strtoupper(Request::get("buscar"))}}"
                </div>
            @endif
          @endif
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h4>Procesos de grado activos</h4>
            </div>
            <div class="float-right">
              <a href="/titulacion/concluidos">Concluidos</a>
            </div>
          </div>
          <hr>
          <table class="table table-striped grados">
            <thead>
              <th>Matrícula</th>
              <th>Alumno</th>
              <th>Acta</th>
              <th>Certificado</th>
              <th>Grado</th>
            </thead>
            <tbody>
              @foreach (\App\titulos::where("avance","<>",100)->get() as $acta)
              @php
              $matricula = $acta->matricula;
              $nombre = \App\alumnosest::where("matricula",$matricula)->first();
              if($nombre == null){
                $nombre = \App\nombres::where("matricula",$matricula)->first();
              }
              $nombre_completo = $nombre->nombre ? $nombre->nombre : "$nombre->nombre_completo $nombre->apat $nombre->amat";
              @endphp
              <tr>
                <td>{{$acta->matricula}}</td>
                <td>
                  <a class="results" href="/titulacion/view?cid={{base64_encode($matricula)}}">
                    {{$nombre_completo}}
                  </a>
                </td>
                <td>{{$acta->acta->avance}}%</td>
                <td>{{$acta->certificado->avance}}%</td>
                <td>{{$acta->avance}}%</td>
              </tr>

              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('styles')
  <style media="screen">
    .btn-buscar{
      display: inline-block;
      height:36px;
      width:20%;
      text-align: center;
      background-color:rgb(21,176,10);
      color:white;
      border:solid #e4e4e4 1px;
      margin-left: -4px;
      z-index:98;
    }
    .form-buscar{
      height:36px;
      width:80%;
      padding-left: 10px;
      border:solid #e4e4e4 1px;
      border-right:none !important;
      border-radius: 5px 0px 0px 5px !important;
    }
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".grados").DataTable(lang);
      $(".form-buscar").focus();
  </script>
@endsection
