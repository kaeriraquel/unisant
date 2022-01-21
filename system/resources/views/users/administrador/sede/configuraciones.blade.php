@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Configuraciones</h4>
    <p class="card-category">Administra las variables de configuración del sistema</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">
        <a href="/sede/config" class="btn btn-sm btn-primary">Nueva variable</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead class="text-primary">
          <tr><th>
            Clave
          </th>
          <th>
            Valor
          </th>
          <th>Acciones</th>
        </tr></thead>
        <tbody>
            @foreach (\App\keys::all() as $s)
              <tr>
                <td>
                  {{$s->key}}
                </td>
                <td>
                  {{$s->value}}
                </td>
                <td>
                  <a rel="tooltip" class="btn btn-success btn-link" href="/sede/config?cid={{md5($s->id)}}" data-original-title="" title="">
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
</div>
@endsection
