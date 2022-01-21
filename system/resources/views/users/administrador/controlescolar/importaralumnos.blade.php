@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')

<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Importar alumnos desde documento</h3>
        <p>
          Consideraciones:
          <ul>
            <li>Añade un documento CSV utilizando <a href="/apoyo/alumnos/csv">esta plantilla</a></li>
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
    <form action="/controlescolar/importaralumnos" method="post" enctype="multipart/form-data">
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

@if (\App\alumnosimportados::count() > 0)
  <div class="card">
    <div class="card-body">
        <table  class="display nowrap lista" style="width:100%"  data-page-length='100'>
          <thead>
            <th>
              Matrícula
            </th>
            <th>
              Nombre
            </th>
            <th>
              Paterno
            </th>
            <th>
              Materno
            </th>
            <th>
              Grado
            </th>
            <th>
              Grupo
            </th>
            <th>
              Periodo
            </th>
            <th>
              RVOE
            </th>
            <th>
              Sede
            </th>
            <th>
              Distri
            </th>
            <th>
              CURP
            </th>
            <th>
              Nacimiento
            </th>
            <th>
              Inscripción
            </th>
            <th>
              Registro
            </th>
            <th>
              Género
            </th>
            <th>
              Teléfono
            </th>
            <th>
              Celular
            </th>
            <th>
              Correo
            </th>
            <th>
              Calle
            </th>
            <th>
              Número
            </th>
            <th>
              Colonia
            </th>
            <th>
              CP
            </th>
            <th>
              Municipio
            </th>
            <th>
              Estado
            </th>
            <th>
              Estatus
            </th>
          </thead>
          <tbody>
            @php
              $grados = ["","Primero","Segundo","Tercero","Cuarto",
              "Quinto","Sexto","Séptimo","Noveno","Décimo","Onceavo","Doceavo"
            ];
            $matriculas = [];
            $errnos = 0;
            $succss = 0;
            @endphp
            @foreach (\App\alumnosimportados::all() as $alu)
              <tr>
                @php
                  $mat = false;
                  if(isset($matriculas[$alu->matricula])){
                    $mat = true;
                  } else {
                    $matriculas[$alu->matricula] = 0;
                  }
                  $al = \App\alumnosest::where("matricula",$alu->matricula)->first();
                  $curp = \App\alumnosest::where("curp",$alu->curp)->first();
                  $status = \App\estadosdelalumno::find($alu->status);
                  $rvoe = \App\revoes::find($alu->rvoe);
                  $sede = \App\sedes::find($alu->sede);
                  $periodo = \App\periodos::find($alu->periodo);

                  if($al || $curp || !$status || !$rvoe || !$sede || !$periodo || $mat)
                  {
                    $errnos++;
                    $alu->insertar = false;
                  }
                  else {
                    $succss++;
                    $alu->insertar = true;
                  }

                  $alu->save();

                @endphp
                <td class="bg-{{!$al?:"danger"}}">
                  {{$al ? $alu->matricula." duplicada" : $alu->matricula}}
                  {!!$mat ? "<span class='bg-danger'>duplicada en esta hoja</span>" : ""!!}
                </td>
                <td>
                  {{$alu->nombre}}
                </td>
                <td>
                  {{$alu->apat}}
                </td>
                <td>
                  {{$alu->amat}}
                </td>
                <td>
                  {{isset($grados[$alu->grado]) ? $grados[$alu->grado] : "No definido"}}
                </td>
                <td>
                  {{$alu->grupo}}
                </td>
                <td class="bg-{{$periodo?:"danger"}}">
                  {{!$periodo ? "$alu->periodo no definido" : "$periodo->periodo"}}
                </td>
                <td class="bg-{{$rvoe?:"danger"}}">
                  {{!$rvoe ? "$alu->rvoe no definido" : "$rvoe->nombre"}}
                </td>
                <td class="bg-{{$sede?:"danger"}}">
                  {{!$sede ? "$alu->sede no definido" : "$sede->sede"}}
                </td>
                <td>
                  {{$alu->grupo_distribucion}}
                </td>
                <td class="bg-{{!$curp?:"danger"}}">
                  {{$curp ? "$alu->curp duplicada" : "$alu->curp"}}
                </td>
                <td
                  @php
                    try{
                     \Carbon\carbon::parse($alu->fecha_nacimiento)->format("Y-m-d");
                      $succss++;
                    } catch(Exception $e){
                      echo "class='bg-danger'";
                      $errnos++;
                    }
                  @endphp
                >
                  @php
                    try{
                      echo \Carbon\carbon::parse($alu->fecha_nacimiento)->format("Y-m-d");
                    } catch(Exception $e){
                      echo "fecha no valida";
                    }
                  @endphp
                </td>
                <td
                @php
                  try{
                   \Carbon\carbon::parse($alu->fecha_inscripcion)->format("Y-m-d");
                    $succss++;
                  } catch(Exception $e){
                    echo "class='bg-danger'";
                    $errnos++;
                  }
                @endphp
                >
                  @php
                    try{
                      echo \Carbon\carbon::parse($alu->fecha_inscripcion)->format("Y-m-d");
                    } catch(Exception $e){
                      echo "fecha no valida";
                    }
                  @endphp
                </td>
                <td
                @php
                  try{
                   \Carbon\carbon::parse($alu->fecha_registro)->format("Y-m-d");
                    $succss++;
                  } catch(Exception $e){
                    echo "class='bg-danger'";
                    $errnos++;
                  }
                @endphp
                >
                  @php
                    try{
                      echo \Carbon\carbon::parse($alu->fecha_registro)->format("Y-m-d");
                    } catch(Exception $e){
                      echo "fecha no valida";
                    }
                  @endphp
                </td>
                <td>
                  {{$alu->genero}}
                </td>
                <td>
                  {{$alu->telefono}}
                </td>
                <td>
                  {{$alu->celular}}
                </td>
                <td>
                  {{$alu->email}}
                </td>
                <td>
                  {{$alu->calle}}
                </td>
                <td>
                  {{$alu->numero}}
                </td>
                <td>
                  {{$alu->colonia}}
                </td>
                <td>
                  {{$alu->cp}}
                </td>
                <td>
                  {{$alu->municipio}}
                </td>
                <td>
                  {{$alu->estado}}
                </td>
                <td class="bg-{{$status?:"danger"}}">
                  {{!$status ? "No definido" : "$status->name"}}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <hr>
        @if ($errnos > 0)
          <div class="alert alert-warning">
            Existen problemas en {{$errnos}} celdas.
          </div>
          <hr>
        @endif
        @if ($succss > 0)
          <div class="alert alert-success">
            {{$succss}} celdas listas para importarse.
          </div>
          <hr>
        @endif
        <div class="clearfix">
          <div class="float-left">
            <form action="/controlescolar/continuaralumnos" method="post">
              @csrf
              <button type="submit" class="btn btn-primary btn-sm" name="button">
                Continuar con la importación
              </button>
            </form>
          </div>
          <div class="float-right">
            <form action="/controlescolar/deshaceralumnosimportados" method="post">
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
      lang.scrollX = true;
      $(".lista").DataTable(lang);
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
