@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header card-header-primary">
    <h4 class="card-title ">Concepto de requerimiento</h4>
    <p class="card-category">Aquí puedes crear/editar conceptos nuevos</p>
  </div>
  <div class="card-body">
    <div class="row">
      @php
        $revo = Request::get("cid") ? \App\conceptos::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
      @endphp
    </div>
    <form action="/requerimientos/{{$revo==null ? "guardar":"actualizar"}}" method="post">
      @csrf
      <label class="text-dark" for="">Concepto</label>
      <input type="text" required name="concepto" value="{{$revo!=null?$revo->concepto:""}}" class="form-control" placeholder="Concepto">
      <label class="text-dark" for="">Usos (<small>0 es indefinido</small>)</label>
      <input type="number" required name="usos" value="{{$revo!=null?$revo->usos:"0"}}" class="form-control" placeholder="Usos">
      <label class="text-dark" for="">¿Activo?</label>
      <select class="form-control" name="activo">
        <option value="0" {{(isset($revo) && $revo->activo == 0) ? "selected" : ""}}>Inactivo</option>
        <option value="1" {{(isset($revo) && $revo->activo == 1) ? "selected" : ""}}>Activo</option>
      </select>
      <input type="submit" class="btn btn-primary" value="{{$revo!=null?"Actualizar":"Guardar"}}">
    </form>
  </div>
</div>
@endsection
