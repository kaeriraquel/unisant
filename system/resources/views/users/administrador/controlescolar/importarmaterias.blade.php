@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Importar materias desde documento</h3>
        <p>
          Descarga la plantilla proporcionada, concluye la captura de datos, revisa la información antes de exportar tu documento, al terminar, carga el documento (Aquellos elementos cuyo contenido no sea válido, serán omitidos durante la importación previa), una vez cargadadas las tuplas de datos, verifica las advertencias señaladas en la tabla inferior, sí existen inconvenientes, el documento puede ser deshechado e importar uno nuevo con las correciones necesarias, sí el documento cumple con las espectativas del usuario, presiona "Continuar con la importación", durante este paso serán omitidos aquellas tuplas cuyos elementos no cumplieran los parámetros considerados válidos.
        </p>
        <p>
          Consideraciones:
          <ul>
            <li>Añade un documento CSV utilizando <a href="/apoyo/calificaciones/csv">esta plantilla</a></li>
            <li>Si existe un documento activo e importas otro, el documento anterior será automáticamente deshechado.</li>
            <li>Evita el uso de <b>,</b> .</li>
            <li>Utiliza los catalogos de folios proporcionados por el administrador.</li>
            <li>En ningun caso deberás colocar nombres de materias o de periodos, solo se admiten folios y matrículas.</li>
            <li>El nombre del documento deberá ser <b>calificaciones.csv</b>.</li>
            <li>Sí alguno de los componentes de la tuplas tiene <b>"Folio no válido"</b> , la tupla completa será omitida al concluir la importación.</li>
          </ul>
        </p>
      </div>
      <div class="float-right">
        <a href="/controlescolar/lista">Regresar</a>
      </div>
    </div>
    <hr>
    <div class="row">
      @php
        $revo = Request::get("cid") ? \App\revoes::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
      @endphp
    </div>
    <form action="/controlescolar/importar" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-1">
          <label for="">Documento CSV:</label>
        </div>
        <div class="col-3">
          <div class="file-upload">
            <div class="file-select">
              <div class="file-select-button" id="fileName">CSV</div>
              <div class="file-select-name" id="noFile">No has seleccionado un documento..</div>
              <input accept=".csv" required type="file" name="chooseFile" id="chooseFile">
            </div>
          </div>
        </div>
        <div class="col">
          <input type="submit" class="btn btn-primary btn-sm" value="Importar">
        </div>
      </div>
    </form>
  </div>
</div>

@if (\App\materiasimportadas::count() > 0)
  <div class="card">
    <div class="card-body">
        <table class="table table-striped materias"  data-page-length='100'>
          <thead>
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
          </thead>
          <tbody>
            @foreach (\App\materiasimportadas::all() as $mat)
              @php
                if(is_numeric($mat->periodo_id)){
                  $fecha = $mat->periodo->periodo;
                }  else {
                  try {
                    $fecha = \Carbon\carbon::parse($mat->periodo_id)->format("Y-m-d");
                  } catch (\Exception $e) {
                    $fecha = null;
                  }

                }
              @endphp
              <tr {{($fecha == null || $mat->alumno == NULL || $mat->materia == NULL || ($mat->calificacion <= 5 && $mat->tiporeprobada == NULL)) ? "class=bg-danger" : ""}}>
                <td>
                  {{$mat->alumno!= NULL ? $mat->alumno->matricula  : "Folio no válido ".$mat->matricula}}
                </td>
                <td>
                  {{$mat->alumno!= NULL ? $mat->alumno->nombre_completo." ".$mat->alumno->apat." ".$mat->alumno->amat  : "Folio no válido"}}
                </td>
                <td>
                  {{$mat->materia != NULL ? $mat->materia->name : "Folio no válido ".$mat->materia_id}}
                </td>
                <td>
                  {{$mat->calificacion}}
                  @if ($mat->calificacion<= 5)
                    ({{$mat->tiporeprobada != NULL ? $mat->tiporeprobada->name : "Folio no válido ".$mat->tiporeprobada_id}})
                  @endif
                </td>
                <td>
                  {{$fecha ?: "Formato de fecha/periodo no válido."}}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <hr>
        <div class="clearfix">
          <div class="float-left">
            <form action="/controlescolar/continuarimportacion" method="post">
              @csrf
              <button type="submit" class="btn btn-primary btn-sm" name="button">
                Continuar con la importación
              </button>
            </form>
          </div>
          <div class="float-right">
            <form action="/controlescolar/limpiarimportados" method="post">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm" name="button">
                Deshechar documento importado
              </button>
            </form>
          </div>
        </div>
    </div>
  </div>
@endif

@endsection
@section('styles')
  <style media="screen">
  .file-upload{display:block;text-align:center;font-family: Helvetica, Arial, sans-serif;font-size: 12px;}
.file-upload .file-select{display:block;border: 2px solid #dce4ec;color: #34495e;cursor:pointer;height:40px;line-height:40px;text-align:left;background:#FFFFFF;overflow:hidden;position:relative;}
.file-upload .file-select .file-select-button{background:#dce4ec;padding:0 10px;display:inline-block;height:40px;line-height:40px;}
.file-upload .file-select .file-select-name{line-height:40px;display:inline-block;padding:0 10px;}
.file-upload .file-select:hover{border-color:#34495e;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload .file-select:hover .file-select-button{background:#34495e;color:#FFFFFF;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload.active .file-select{border-color:#3fa46a;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload.active .file-select .file-select-button{background:#3fa46a;color:#FFFFFF;transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;}
.file-upload .file-select input[type=file]{z-index:100;cursor:pointer;position:absolute;height:100%;width:100%;top:0;left:0;opacity:0;filter:alpha(opacity=0);}
.file-upload .file-select.file-select-disabled{opacity:0.65;}
.file-upload .file-select.file-select-disabled:hover{cursor:default;display:block;border: 2px solid #dce4ec;color: #34495e;cursor:pointer;height:40px;line-height:40px;margin-top:5px;text-align:left;background:#FFFFFF;overflow:hidden;position:relative;}
.file-upload .file-select.file-select-disabled:hover .file-select-button{background:#dce4ec;color:#666666;padding:0 10px;display:inline-block;height:40px;line-height:40px;}
.file-upload .file-select.file-select-disabled:hover .file-select-name{line-height:40px;display:inline-block;padding:0 10px;}
  </style>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table").DataTable(lang);
      $(".del").bind("click",function(){

          Swal.fire({
            icon: 'warning',
            title: '¿Deseas {{Request::has("ar") ? "desa" : "a"}}rchivar la materia?',
            text: "{{Request::has("ar") ? "Si eliminas una materia, esta quedará archivada en el sistema y no será mostrada en la lista principal" : "La materia será visible en la materias corrientes"}}",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              let e = $(this);
              ShowWaitNotify('Moviendo');
              $.post("/controlescolar/switchmat?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                console.log(e.parent().parent().remove());
                ShowSuccessNotify("Archivado");
              });
            }
          });

      });

      $('#chooseFile').bind('change', function () {
        var filename = $("#chooseFile").val();
        if (/^\s*$/.test(filename)) {
          $(".file-upload").removeClass('active');
          $("#noFile").text("No file chosen...");
        }
        else {
          $(".file-upload").addClass('active');
          $("#noFile").text(filename.replace("C:\\fakepath\\", ""));
        }
      });

  </script>
@endsection
