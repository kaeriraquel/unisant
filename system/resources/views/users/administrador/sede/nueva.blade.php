@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>
          Sede
        </h3>
      </div>
      <div class="float-right">
        <a href="/sede/lista">
          Regresar
        </a>
      </div>
    </div>
    <hr>
    <div class="row">
      @php
        $sede = Request::get("cid") ? \App\sedes::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
      @endphp
    </div>
    <form action="/sedes/{{$sede==null ? "guardar":"actualizar"}}" method="post">
      @csrf
      <label for="">Nombre de la sede</label>
      <input type="text" required name="sede" value="{{$sede!=null?$sede->sede:""}}" class="allow form-control" placeholder="Sede">
      <label for="">Precio de pago por defecto</label>
      <input type="number" required name="monto" value="3800" class="allow form-control" placeholder="1520">
      <label for="">
        Divisa
      </label>
      <select class="allow form-control" required name="divisa">
        <option value="">Selecciona una divisa</option>
        @foreach (\App\divisas::all() as $div)
          <option {{isset($sede) ? ($div->id == $sede->divisa_id ? "selected" : "") : ""}} value="{{$div->id}}">{{$div->divisa}}</option>
        @endforeach
      </select>
      <label for="">Api de consulta</label>
      <input type="text" name="todos" value="{{$sede!=null?$sede->todos:""}}" class="allow form-control" placeholder="http:// ...">
      <label for="">Api de consulta individual</label>
      <input type="text" name="individual" value="{{$sede!=null?$sede->individual:""}}" class="allow form-control" placeholder="http:// ...">
      <hr>

      <input type="submit" class="btn btn-primary" value="{{$sede!=null?"Actualizar":"Guardar"}}">
    </form>
  </div>
</div>
@endsection
