@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          @php
              $user = auth()->user();
              $acc = $user->accesos;
          @endphp
          <h3>
            Acceso de sedes de {{$user->name}}
          </h3>
          <p>
            El acceso a sedes permite a determinados usuarios acceder a otras cuentas de usuarios aún cuando la sede de control es distinta del usuario original.
          </p>
        </div>
        <div class="float-right">
          <a href="/home">Regresar</a>
        </div>
      </div>
      <hr>
      <form method="post" action="/sedes/setaccess" autocomplete="off" class="form-horizontal">
        @csrf
        <input type="hidden" name="owner_id" value="{{$user->id}}">
        <div class="row">
          <div class="col-4">
            <label for="">
              Selecciona:
            </label>
            <select class="allow form-control" required name="sede_id">
              <option value="">Seleccione</option>
              @foreach ($acc as $ac)
                @php
                  $sede = $ac->sede;
                @endphp
                <option value="{{$sede->id}}">{{$sede->sede}}</option>
              @endforeach
            </select>
          </div>
          <div class="col">
            <br>
            <input type="submit" value="Hacer login" class="btn btn-primary">
          </div>
        </div>
      </form>
    </div>
  </div>

@endsection
@section('scripts')

@endsection
