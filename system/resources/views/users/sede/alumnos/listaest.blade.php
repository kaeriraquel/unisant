@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header">
    <h4 class="card-title ">Alumnos estatales</h4>
    <p class="card-category">Todos los alumnos estatales de mi sede</p>
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
              Estado
            </th>
            <th>
              Matricula
            </th>
            <th>
              Nombre
            </th>
            <th>
              Curp
            </th>
            <th>
              Facturación
            </th>
            <th>
              Grupo
            </th>
            <th>
              Sede
            </th>
          </tr>
        </thead>
        <tbody>
          @php
            if(\Auth::user()->nivel->name == "Administrador"){
              $data = \App\alumnosest::all();
            } else {
              $data = \App\alumnosest::where("sede_id",\Auth::user()->sede->sede->id)->get();
            }
          @endphp
          @foreach ($data as $al)
              <tr>
                <td>
                  @php
                    $estado = $al->baja == NULL ? \App\estadosdelalumno::where("estado","1")->first() : (\App\estadosdelalumno::where("id",$al->baja)->first() != NULL ? \App\estadosdelalumno::where("id",$al->baja)->first() : "No definido");
                  @endphp
                  <div class="badge" style="background-color:{{$estado->background}};color:{{$estado->color}};">
                    {{$estado->name}}
                  </div>
                </td>
                <td>
                  {{$al->matricula}}
                </td>
                <td>
                  <a href="/alumnos/pagos?cid={{base64_encode($al->matricula)}}">
                    {{$al->nombre_completo}}
                    {{$al->apat}}
                    {{$al->amat}}
                  </a>
                </td>
                <td>
                  {{$al->curp}}
                </td>
                <td>
                  {{$al->facturacion ? "Facturación" : "Vacío"}}
                </td>
                <td>
                  {{$al->grupo ? $al->grupo->grupo : "Sin grupo"}}
                </td>
                <td>
                  {{$al->sede->sede}}
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
