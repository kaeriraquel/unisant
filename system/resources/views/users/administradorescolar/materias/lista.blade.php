@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $acc = \Auth::user()->accesos;
    $accesos = [];
    $i = 0;
    foreach ($acc as $ac) {
      $accesos[$i++] = $ac->sede->id;
    }

    $materias = \App\alumnos_materias::whereHas("alumno",function($query) use ($accesos){
      $query->whereHas("sede",function($q) use ($accesos){
        $q->whereIn("sede_id",$accesos);
      });
    })->where("estado",NULL)->get();

  @endphp
  <form class="con" action="/controlescolar/bloquematerias" method="post">
    @csrf
    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-left">
            <h3>Aprobar materias</h3>
          </div>
          <div class="float-right">
          </div>
        </div>
        <hr>
          <table class="table table-striped materias">
            <thead>
              <th>
                <input type="checkbox" class="elcheck" id="elcheck" value="true">
              </th>
              <th>
                Folio
              </th>
              <th>
                Matrícula
              </th>
              <th>
                Alumno
              </th>
              <th>
                Materia
              </th>
              <th>
                Calificación
              </th>
              <th>
                Periodo
              </th>
              <th>
                Sede
              </th>
              <th>
                Acciones
              </th>
          </thead>
          <tbody>
              @foreach ($materias as $mat)
                <tr>
                  <td>
                    <input type="checkbox" class="mats" name="materias[]" id="check_{{$mat->id}}" value="{{$mat->id}}">
                  </td>
                  <td>{{\Carbon\Carbon::parse($mat->created_at)->format("Y")}}{{$mat->id}}</td>
                  <td>{{$mat->alumno->matricula}}</td>
                  <td>{{$mat->alumno->nombre_completo." ".$mat->alumno->apat." ".$mat->alumno->amat}}</td>
                  <td>
                    {{$mat->materia->name}}
                  </td>
                  <td>
                    {{$mat->calificacion}}
                    @if ($mat->calificacion <= 5)
                      {{$mat->reprobada->name}}
                    @endif
                  </td>
                  <td>
                    @php
                    if(is_numeric($mat->periodo_id)){
                      echo $mat->periodo->periodo;
                    }  else {
                      try {
                        echo \Carbon\carbon::parse($mat->periodo_id)->format("Y-m-d");
                      } catch (\Exception $e) {
                        echo "Fecha/periodo no válido";
                      }

                    }
                    @endphp
                  </td>
                  <td>
                    {{$mat->alumno->sede->sede}}
                  </td>
                  <td>
                    <a href="#" class="switch" cid={{md5($mat->id)}}>
                      Aprobar
                    </a>
                  </td>
                </tr>
              @endforeach
          </tbody>
          </table>
          <hr>
          <button type="button" class="aprobar btn btn-primary">
            Aprobar toda la selección
          </button>
      </div>
    </div>
  </form>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(() => {
      let dTable = $(".materias").DataTable(lang);
      setEvents();
      dTable.on("draw.dt",() => {
        setEvents();
      });
    });

    let setEvents = function(){
      $(".switch").bind("click",function(){
          Swal.fire({
            icon: 'warning',
            title: '¿Deseas cambiar el estado de la materia?',
            text: "La materia será aprobada o regresada a en proceso.",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Moviendo');
              let e = $(this);
              $.post("/controlescolar/switchmateria?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                e.text((e.text().indexOf("Aprobar") != -1) ? "Cancelar" : "Aprobar");
                ShowSuccessNotify("Intercambiado");
              });
            }
          });

      });
    }

    $(".elcheck").bind("click",function(){
      $("input[type='checkbox']").prop('checked', $(this).is(':checked'));
    });

    $(".aprobar").bind("click",function(){
      if($(".mats:checked").length > 0){
        $(".con").submit();
      } else {
        Swal.fire(
          '¡Ups!',
          'Debes de seleccionar al menos una materia de la lista',
          'warning'
        )
      }
    });
  </script>
@endsection
