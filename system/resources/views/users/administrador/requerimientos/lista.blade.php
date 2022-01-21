@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>
          Lista de conceptos de requerimientos
        </h3>
      </div>
      <div class="float-right">
        <a href="/requerimientos/nuevo" class="btn btn-sm btn-primary">Nuevo concepto</a>
      </div>
    </div>
    <hr>
      <table class="table table-striped">
        <thead class=" text-primary">
          <tr>
            <th>
              Concepto
            </th>
            <th>
              Usos
            </th>
            <th>
              Activo
            </th>
            <th class="text-right">
              Acciones
            </th>
        </tr>
      </thead>
        <tbody>
            @foreach (\App\conceptos::all() as $r)
              <tr>
                <td>
                  {{$r->concepto}}
                </td>
                <td>
                  {{$r->usos}}
                </td>
                <td>
                  {{$r->activo == 0 ? "Inactivo" : "Activo"}}
                </td>
                <td class="td-actions text-right">
                  <a rel="tooltip" class="btn btn-success btn-link" href="/requerimientos/nuevo?cid={{md5($r->id)}}" data-original-title="" title="">
                    <i class="material-icons">edit</i>
                    <div class="ripple-container"></div>
                  </a>
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
    $(".table").DataTable(lang);
  </script>
@endsection
