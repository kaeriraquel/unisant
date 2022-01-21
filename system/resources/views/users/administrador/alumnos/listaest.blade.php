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
            $data = \App\alumnosest::all();
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
@section('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
@endsection
@section('scripts')
  <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
  <script type="text/javascript">
    $(".alumnos").DataTable({
        dom: 'Bfrtip',
        "language":lang.language,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
  </script>
@endsection
