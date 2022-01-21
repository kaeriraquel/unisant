@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>RVOE</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="/sede/revoe?cid={{Request::get("cid")}}" class="btn btn-sm btn-primary">AGREGAR RVOE</a>
      </div>
    </div>
    <hr>
    @php
      $sede = \App\sedes::whereRAW("md5(id)='".Request::get("cid")."'")->first();
    @endphp
      <table class="table table-striped">
        <thead class=" text-primary">
          <tr>
            <th>
              Clave
            </th>
            <th>
              RVOE
            </th>
            <th>
              Alumnos RVOE-Sede
            </th>
            <th class="text-right">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody>
            @foreach ($sede->revoes as $rs)
              <tr>
                <td>
                  {{$rs->revoe->clave}}
                </td>
                <td>
                  {{$rs->revoe->nombre}}
                </td>
                <td>
                  {{\App\alumnosest::where("revoe_id",$rs->revoe->id)->count()}}
                </td>
                <td class="td-actions text-right">
                  <form action="/revoes/eliminar" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{md5($rs->id)}}">
                    <button type="submit" class="btn btn-link">
                      <i class="fa fa-trash text-danger"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
        </tbody>
      </table>
  </div>
</div>
@endsection
