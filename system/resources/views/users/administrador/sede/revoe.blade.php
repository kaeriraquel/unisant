@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">RVOE - Sede</h4>
    <p class="card-category">Aquí puedes editar los RVOE de la sede</p>
  </div>
  <div class="card-body">
    <div class="row">
      @php
        $sede = Request::get("cid") ? \App\sedes::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
      @endphp
    </div>
    <form action="/revoes/guardarensede" method="post">
      @csrf
      <input type="hidden" name="sede_id" value="{{$sede->id}}">
      <label for="">Nombre de la sede</label>
      <select class="form-control" required name="revoe_id">
        <option value="">Selecciona</option>
        @foreach (\App\revoes::all() as $revoe)
          <option value="{{$revoe->id}}">{{$revoe->clave}} - {{$revoe->nombre}}</option>
        @endforeach
      </select>
      <input type="submit" class="btn btn-primary" value="Guardar">
    </form>
  </div>
</div>
@endsection
