@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          @php
            $user = (\App\User::whereRAW("md5(id)='".Request::get("cid")."'")->first()) ?: NULL;
          @endphp
          <form method="post" action="/ProfileController/{{$user!=NULL ?"actualizar":"nuevo"}}" autocomplete="off" class="form-horizontal">
            @csrf
            <div class="card">
              <div class="card-body ">
                <div class="clearfix">
                  <div class="float-left">
                    <h3>Usuarios</h3>
                    @if ($user != NULL)
                      <p class="card-category">Aquí puedes modificar un usuaro existente</p>
                        @else
                      <p class="card-category">Aquí puedes crear un nuevo usuario</p>
                    @endif
                  </div>
                  <div class="float-right">
                    <a href="/user">Regresar</a>
                  </div>
                </div>
                <hr>
                @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
                <div class="row">
                  <label class="col-sm-2 col-form-label">Nombre</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="input-name" type="text" placeholder="Nombre" value="{{$user->name ?? ""}}" required="true" aria-required="true"/>
                      @if ($errors->has('name'))
                        <span id="name-error" class="error text-danger" for="input-name">{{ $errors->first('name') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Correo electrónico</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="input-email" type="email" placeholder="Correo electrónico" value="{{$user->email ?? ""}}" required />
                      @if ($errors->has('email'))
                        <span id="email-error" class="error text-danger" for="input-email">{{ $errors->first('email') }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Sede</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      @php
                      @endphp
                      <select class="form-control" name="sede_id" required>
                        <option value="">Seleccione</option>
                        @foreach (\App\sedes::all() as $s)
                          <option {{$user != NULL && $user->sede != NULL && ($user->sede ? ($user->sede->sede ? $user->sede->sede->id : 0) : 0) == $s->id ? "selected": ""}}  value="{{$s->id}}">{{$s->sede}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Nivel de usuario</label>
                  <div class="col-sm-7">
                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                      @php
                      @endphp
                      <select class="form-control" name="nivel_id" required>
                        <option value="">Seleccione</option>
                        @foreach (\App\nivel::all() as $s)
                          <option {{$user != NULL && $user->nivel != NULL && $user->nivel->id == $s->id ? "selected": ""}}  value="{{$s->id}}">{{$s->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">Clave</label>
                  <div class="col-sm-7">
                    <div class="form-group">
                      @php
                        $pass = "";
                        if($user == NULL){
                          $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                          for($i = 1; $i <= 12;$i++){
                            $pass .= substr($chars,rand(0,strlen($chars)),1);
                          }
                        }
                      @endphp
                      <input class="form-control" name="password" id="input-pass" type="text" placeholder="Clave" value="{{$pass}}" {{$user ? "" : "required"}} />
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer ml-auto mr-auto">
                <button type="submit" id="generate-password" class="gen btn btn-primary">Guardar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')

@endsection
