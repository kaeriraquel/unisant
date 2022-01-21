@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<form class="con" action="/planes/setall" method="post">
  @csrf
  <div class="card">
    <div class="card-header">
      <h4 class="card-title ">Alumnos</h4>
      <p class="card-category">Sin fechas de plan asignadas</p>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-12 text-right">

        </div>
      </div>
        <table class="table alumnos" data-page-length='200'>
          <thead class=" text-primary">
            <tr>
              <th>
                <input type="checkbox" class="elcheck" id="elcheck" value="true">
              </th>
              <th>
                Matricula
              </th>
              <th>
                Nombre
              </th>
              <th>
                Grupo
              </th>
              <th>
                Sede
              </th>
            </tr>
          </thead>
          <tbody>
            @php
              $data = \App\alumnosest::where("sede_id",\Auth::user()->sede->sede->id)->whereHas("planespago",function($query){
                $query->where("every",NULL)->where("since",NULL);
              })->get();
              $data2 = \App\nombres::where("sede_id",\Auth::user()->sede->sede->id)->whereHas("planespago",function($query){
                $query->where("every",NULL)->where("since",NULL);
              })->get();
            @endphp
            @foreach ($data as $al)
                <tr>
                  <td>
                    <input type="checkbox" class="planes" name="planes[]" id="check_{{$al->id}}" value="{{$al->matricula}}">
                  </td>
                  <td>
                    {{$al->matricula}}
                  </td>
                  <td>
                    <a href="/alumnos/planes?cid={{base64_encode($al->matricula)}}">
                      {{$al->nombre_completo}}
                      {{$al->apat}}
                      {{$al->amat}}
                    </a>
                  </td>
                  <td>
                    {{$al->grupo ? $al->grupo->grupo : "Sin grupo"}}
                  </td>
                  <td>
                    {{$al->sede->sede}}
                  </td>
                </tr>
            @endforeach
            @foreach ($data2 as $al)
                <tr>
                  <td>
                    <input type="checkbox" class="planes" name="planes[]" id="check_{{$al->id}}" value="{{$al->matricula}}">
                  </td>
                  <td>
                    {{$al->matricula}}
                  </td>
                  <td>
                    <a href="/alumnos/planes?cid={{base64_encode($al->matricula)}}">
                      {{$al->nombre}}
                    </a>
                  </td>
                  <td>
                    {{$al->grupo ? $al->grupo->grupo : "Sin grupo"}}
                  </td>
                  <td>
                    {{$al->sede->sede}}
                  </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <hr>
        <div class="row">
          <div class="col">
            <label for="">Fecha de inicio:</label>
            <input type="date" class="form-control allow desde" name="since" value="" required>
          </div>
          <div class="col">
            <label for=""><a href="#" class="question"><i class="fas fa-question-circle"></i> Cada:</a></label>
            <input type="number" step="1" max="31" min="1" class="form-control allow hasta" name="every" value="31" required>
          </div>
          <div class="col">
            <label for=""><a href="#" class="bq"><i class="fas fa-question-circle"></i> Beca:</a></label>
            <input type="number" step="0.1" max="10000" min="0" class="form-control allow dias" name="beca" value="0" required>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <br>
            <button class="btn btn-primary crear">Asignar</button>
          </div>
        </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".question").bind("click", () => {
      event.preventDefault();
      Swal.fire("Ayuda","Sí eliges entre 1 y 30, transcurriran esas cantidad de días entre cada pago, sí eliges 31, el pago será requerido el mismo día de la fecha de inicio.","question");
    });
    $(".bq").bind("click", () => {
      event.preventDefault();
      Swal.fire("Ayuda","Coloca el monto de la colegiatura sin beca, la beca se calculará en función; Sí colocas 0, beca no saldrá en el estado de cuenta.","question");
    });
    $(".alumnos").DataTable(lang);
    $(".elcheck").bind("click",function(){
      $("input[type='checkbox']").prop('checked', $(this).is(':checked'));
    });
    $(".crear").bind("click",function(){
      if($(".planes:checked").length > 0 && $(".dias").val() != "" && $(".desde").val()!= "" && $(".hasta").val() != ""){
        $(".con").submit();
      } else {
        Swal.fire(
          '¡Ups!',
          'Debes de seleccionar al menos un pago, escribir una fecha, tiempo entre pagos y monto total sin beca.',
          'warning'
        )
      }
    });
  </script>
@endsection
