@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          @php
              $user = (\App\User::whereRAW("md5(id)='".Request::get("cid")."'")->first()) ?: NULL;
              $acc = $user->accesos;
          @endphp
          <h3>
            {{$user->name}} -
            Acceso a sedes
          </h3>
          <p>
            El acceso a sedes permite a determinados usuarios acceder a otras cuentas de usuarios aún cuando la sede de control es distinta del usuario original.
          </p>
        </div>
        <div class="float-right">
          <a href="/user">Regresar</a>
        </div>
      </div>
      <hr>
      <form method="post" action="/sedes/addaccess" autocomplete="off" class="form-horizontal">
        @csrf
        <input type="hidden" name="owner_id" value="{{$user->id}}">
        <div class="row">
          <div class="col-4">
            <label for="">
              Selecciona:
            </label>
            <select class="allow form-control" required name="sede_id">
              <option value="">Seleccione</option>
              @foreach (\App\sedes::all() as $sede)
                <option value="{{$sede->id}}">{{$sede->sede}}</option>
              @endforeach
            </select>
          </div>
          <div class="col">
            <br>
            <input type="submit" value="Agregar acceso a sede" class="btn btn-primary">
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <h3>Accesos concedidos</h3>
      <hr>
      @if (count($acc) > 0)
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Sede</th>
              <th>Fecha de alta</th>
              <th>Acciones</th>
            </tr>
          </thead>
          @foreach ($acc as $ac)
            <tr>
              <td>{{\Carbon\carbon::parse($ac->create_at)->format("Y")}}{{$ac->id}}</td>
              <td>{{$ac->sede->sede}}</td>
              <td>{{$ac->created_at}}</td>
              <td>
                <form action="/sedes/delaccess" method="post">
                  @csrf
                  <input type="hidden" name="cid" value="{{md5($ac->id)}}">
                  <button type="submit" class="btn btn-link">
                    <i class="fa fa-trash text-danger"></i>
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </table>
        @else
          <h3 class="text-center">
            <i class="fas fa-exclamation-triangle text-warning"></i> No hay accesos concedidos a este usuario
          </h3>
      @endif
    </div>
  </div>
@endsection
@section('scripts')

@endsection
