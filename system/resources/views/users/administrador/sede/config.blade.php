@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Configuaciones</h4>
    <p class="card-category">Agrega nuevas Configuaciones al sistema</p>
  </div>
  <div class="card-body">
    <div class="row">
      @php
        $keys = Request::get("cid") ? \App\keys::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
      @endphp
    </div>
    <form action="/sedes/{{$keys!=null?"actualizar":"guardar"}}key" method="post">
      @csrf
      <label for="">Clave</label>
      <input type="text" required name="key" value="{{$keys!=null?$keys->key:""}}" class="form-control" placeholder="Clave">
      <label for="">Valor</label>
      <input type="number" required name="value" value="{{$keys!=null?$keys->value:""}}" class="form-control" placeholder="Valor">
      <input type="submit" class="btn btn-primary" value="{{$keys!=null?"Actualizar":"Guardar"}}">
    </form>
  </div>
</div>
@endsection
