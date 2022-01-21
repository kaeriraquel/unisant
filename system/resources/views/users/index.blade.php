@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @if (auth()->user()->nivel->name == "Administrador")
    <div class="card">

      <div class="card-body">
        <div class="clearfix">
          <div class="float-right">
            <br>
            <a href="/usuarios/nuevo" class="btn btn-sm btn-primary">Nuevo usuario</a>
          </div>
          <div class="float-left">
            <h3>
              Lista de usuarios
            </h3>
          </div>
        </div>
        <hr>
        <div class="table-responsive">
          <table class="table table-striped usuarios" data-page-length="500">
            <thead class=" text-primary">
              <tr><th>
                Nombre asociado
              </th>
              <th>
                Nombre de usuario
              </th>
              <th>
                Fecha de creación
              </th>
              <th>
                Sede
              </th>
              <th>
                Nivel
              </th>
              <th class="text-right">
                Acciones
              </th>
            </tr></thead>
            <tbody>
              @foreach (\App\sede_usuario::orderBy("sede_id")->get() as $au)
                <tr>
                  @php
                    $u = $au->usuario;
                  @endphp
                  <td>{{$u->name}}</td>
                  <td>{{$u->email}}</td>
                  <td>{{$u->created_at}}</td>
                  <td>{{isset($u->sede) ? ($u->sede->sede ? $u->sede->sede->sede : "No sede ".$u->sede->sede_id) : "Sin sede"}}</td>
                  <td>{{$u->nivel->name}}</td>
                  <td class="td-actions text-right">
                    <div class="row">
                      <div class="col">
                        <a class="btn btn-success btn-link" href="/usuarios/nuevo?cid={{md5($u->id)}}">
                          <i class="material-icons">edit</i>
                          <div class="ripple-container"></div>
                        </a>
                      </div>
                      <div class="col">
                        <a class="btn btn-success btn-link" href="/usuarios/access?cid={{md5($u->id)}}">
                          <i class="fas fa-users"></i>
                        </a>
                      </div>
                      <div class="col">
                        <form action="/HomeController/log?cid={{md5($u->id)}}" method="post">
                          @csrf
                          <button class="btn btn-link sign" type="submit">
                            <i class="fas fa-sign-in-alt text-success"></i>
                          </button>
                        </form>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @else
    <h3 class="text-center">
      <i class="fas fa-exclamation-triangle text-warning"></i> Zona no accesible para este usuario
    </h3>
  @endif
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".usuarios").on("init.dt", () => {
      $("input[type=search]").focus();
      $("input[type=search]").bind("keypress",(e) => {
        if(e.keyCode == 13){
          $(".sign")[0].click();
        }
      });
    }).DataTable(lang);
  </script>
@endsection
