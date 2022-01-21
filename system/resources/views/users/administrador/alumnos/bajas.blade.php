@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Lista de alumnos</h4>
    <p class="card-category">Alumnos con Baja</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">

      </div>
    </div>
      <table class="table alumnos">
        <thead class=" text-primary">
          <tr>
            <th>
              Estado
            </th>
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
              Autorizar
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
          @foreach (\App\alumnosest::where("baja",2)->get() as $al)
              <tr>
                <td>
                  <div class="text-{{$al->baja == NULL ? 'success' : ($al->baja == 1 ? "warning" : "danger")}}">
                    <span class="material-icons">
                    trip_origin
                    </span>
                  </div>
                </td>
                <td>{{$al->baja == NULL ? 'Activo' : ($al->baja == 1 ? "En proceso de baja" : "Baja")}}</td>
                <td>
                  {{$al->matricula}}
                </td>
                <td>
                    {{$al->nombre_completo}}
                </td>
                <td>
                  <form action="/alumnos/cancelar" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{base64_encode($al->matricula)}}">
                    <button class="btn btn-success" type="submit">
                      <i class="fas fa-user-edit"></i> Alta
                    </button>
                  </form>
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
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <a class="btn btn-link text-info" href="/alumnos/devoluciones">Historial de devoluciones</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".alumnos").DataTable(lang);
  </script>
@endsection
