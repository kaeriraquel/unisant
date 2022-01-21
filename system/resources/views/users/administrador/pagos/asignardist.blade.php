@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
@php
  $dist = \App\distribuciones::whereRAW("md5(id)='".Request::get("cid")."'")->first();
  $grupos = $dist->dist_grupos;
  $grupos_id = [];
  $i = 0;
  foreach ($grupos as $dist_grupos) {
    $grupos_id[$i++] = $dist_grupos->grupo;
  }
  $menu = [
    "Regresar" => "/pagos/distribuciones"
  ];
@endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Añadir grupos a la distribución</h3>
      </div>
      <div class="float-right">
        <a href="/pagos/distribuciones">
          Regresar
        </a>
      </div>
    </div>
    <hr>
    <form class="input" action="/dist/addgrupo" method="post">
      @csrf
      <div class="row">
        <div class="col-4">
          <label for="">Selecciona el grupo:</label>
          <input type="hidden" name="dist_id" value="{{$dist->id}}">
          <input autocomplete="off" type="text" class="allow form-control" name="grupo" list="grouplist" placeholder="Escriba o seleccione un grupo existente">
          @php
            $col = DB::table('grupos')
                   ->select("grupo")
                   ->groupBy('grupo')
                   ->get();
          @endphp
          <datalist id="grouplist">
            @foreach ($col as $grupo)
              <option value="{{$grupo->grupo}}">
            @endforeach
          </datalist>
        </div>
        <div class="col-4">
          <br>
          <input class="btn btn-primary" type="submit" name="" value="Añadir">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <h3>Grupos para {{$dist->distribucion}}</h3>
    <hr>
      @if(count($grupos) > 0)
        <table class="table table1">
          <thead>
            <th>Folio</th>
            <th>Grupo</th>
            <th></th>
          </thead>
          <tbody>
            @foreach ($grupos as $dist_grupo)
              @php
                $gp = $dist_grupo->grupo;
              @endphp
              <tr>
                <td>{{$gp}}</td>
                <td>
                  <form action="/dist/delgrupo" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$dist_grupo->id}}">
                    <button type="submit" class="btn btn-link">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
      <h3 class="text-center">
        <i class="fas fa-exclamation-triangle text-warning"></i> Sin grupos asignados a esta distribución
      </h3>
    @endif
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table1").DataTable(lang);
  </script>
@endsection
