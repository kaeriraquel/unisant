@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $matricula = base64_decode(Request::get("cid"));
    $nombre = \App\alumnosest::where("matricula",$matricula)->first();
    if($nombre == null){
      $nombre = \App\nombres::where("matricula",$matricula)->first();
    }
    $nombre_completo = $nombre->nombre ? $nombre->nombre : "$nombre->nombre_completo $nombre->apat $nombre->amat";
    $acta = \App\actas::where("matricula",$matricula)->first();
    $certificado = \App\certificados::where("matricula",$matricula)->first();
    $titulo = \App\titulos::where("matricula",$matricula)->first();

  @endphp
  <form action="/controlescolar/{{$acta ? "actualizarproceso" : "activarproceso"}}" method="post">
    @csrf
    <input type="hidden" name="matricula" value="{{$matricula}}">
    <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h4>Proceso de grado</h4>
              <small>
                Alumno: {{$nombre_completo}}<br>
                Matrícula: {{$matricula}}
              </small>
            </div>
            <div class="float-right">

            </div>
          </div>
          <hr>
          @php
            $val = $acta ? $acta->avance : 0;
            $status = $acta ? ($acta->avance == 100 ? "bg-success" : "") : "bg-light";
          @endphp
          <div class="row">
            <div class="col-12">
              <h4><i class="fas fa-file-signature"></i>
              Acta</h4>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated {{$status}} actabarra" role="progressbar" aria-valuenow="{{$val}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$val}}%"></div>
              </div>
              <div class="row">
                <div class="col">
                  <input {{$acta ? "" : "disabled"}} class="d-none form-control {{$acta ? "" : "not"}}allow actacampo" type="number" min="0" max="100" step="10" name="avance_acta" value="{{$val}}">
                  <label for="">Estado:</label>
                  <select class="allow form-control acta" name="estado_acta">
                    <option value="">Seleccione</option>
                    @php
                      $parametros = \App\parametros::whereIn("type",["Estado"])->where("fora","acta")->where("sede_id",auth()->user()->sede->sede->id)->get();
                      $campos = \App\parametros::whereIn("type",["Campo","CampoFecha"])->where("fora","acta")->where("sede_id",auth()->user()->sede->sede->id)->get();
                      $percent = 0;
                      $jumps = 100/count($parametros);
                      $i = 1;
                    @endphp
                    @foreach ($parametros as $param)
                      @php
                        $percent = $jumps * $i++;
                      @endphp
                      <option percent="{{$percent}}" {{(isset($acta) && $param->id == $acta->estado) ? "selected" : ""}} value="{{$param->id}}">{{$percent}}% - {{$param->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                @foreach ($campos as $campo)
                  @php
                    $type = $campo->type == "Campo" ? "text" : "date";
                    $paramo = \App\valores_parametros::where("matricula",$matricula)->where("parametro_id",$campo->id)->first();
                    $value = $paramo ? $paramo->value : "";
                  @endphp
                  <div class="col-3">
                    <label for="">{{$campo->id}}{{$campo->name}}</label>
                    <input class="form-control allow" name="{{"campo_$campo->id"}}" placeholder="{{$campo->name}}" type="{{$type}}" value="{{$value}}">
                  </div>
                @endforeach
              </div>
              <hr>
            </div>
            <div class="col-12">
              @php
                $val = $certificado ? $certificado->avance : 0;
                $status = $certificado ? ($certificado->avance == 100 ? "bg-success" : "") : "bg-light";
              @endphp
              <h4><i class="far fa-file"></i>
              Certificado</h4>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated {{$status}} certificadobarra" role="progressbar" aria-valuenow="{{$val}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$val}}%"></div>
              </div>
              <div class="row">
                <div class="col">
                  <input {{$certificado ? "" : "disabled"}} class="d-none form-control {{$certificado ? "" : "not"}}allow certificadocampo" type="number" min="0" max="100" step="10" name="avance_certificado" value="{{$val}}">
                  <label for="">Estado:</label>
                  <select class="allow form-control certificado" name="estado_certificado">
                    <option value="">Seleccione</option>
                    @php
                      $parametros = \App\parametros::whereIn("type",["Estado"])->where("fora","certificado")->where("sede_id",auth()->user()->sede->sede->id)->get();
                      $campos = \App\parametros::whereIn("type",["Campo","CampoFecha"])->where("fora","certificado")->where("sede_id",auth()->user()->sede->sede->id)->get();
                      $percent = 0;
                      $jumps = 100/count($parametros);
                      $i = 1;
                    @endphp
                    @foreach ($parametros as $param)
                      @php
                        $percent = round($jumps * $i++,0);
                      @endphp
                      <option percent="{{$percent}}" {{(isset($certificado) && $param->id == $certificado->estado) ? "selected" : ""}} value="{{$param->id}}">{{$percent}}% - {{$param->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                @foreach ($campos as $campo)
                  @php
                    $type = $campo->type == "Campo" ? "text" : "date";
                    $paramo = \App\valores_parametros::where("matricula",$matricula)->where("parametro_id",$campo->id)->first();
                    $value = $paramo ? $paramo->value : "";
                  @endphp
                  <div class="col-3">
                    <label for="">{{$campo->name}}</label>
                    <input class="form-control allow" name="{{"campo_$campo->id"}}" placeholder="{{$campo->name}}" type="{{$type}}" value="{{$value}}">
                  </div>
                @endforeach
              </div>
              <hr>
            </div>
            <div class="col-12">
              @php
                $val = $titulo ? $titulo->avance : 0;
                $status = $titulo ? ($titulo->avance == 100 ? "bg-success" : "") : "bg-light";
              @endphp
              <h3><i class="fas fa-book"></i>
              Grado</h3>
              <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated {{$status}} gradobarra" role="progressbar" aria-valuenow="{{$val}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$val}}%"></div>
              </div>
              <div class="row">
                <div class="col">
                  <input {{$titulo ? "" : "disabled"}} class="d-none form-control {{$titulo ? "" : "not"}}allow gradocampo" type="number" min="0" max="100" step="10" name="avance_titulo" value="{{$val}}">
                  <label for="">Estado:</label>
                  <select class="allow form-control grado" name="estado_titulo">
                    <option value="">Seleccione</option>
                    @php
                      $parametros = \App\parametros::whereIn("type",["Estado"])->where("fora","grado")->where("sede_id",auth()->user()->sede->sede->id)->get();
                      $campos = \App\parametros::whereIn("type",["Campo","CampoFecha"])->where("fora","grado")->where("sede_id",auth()->user()->sede->sede->id)->get();
                      $percent = 0;
                      $jumps = 100/count($parametros);
                      $i = 1;
                    @endphp
                    @foreach ($parametros as $param)
                      @php
                        $percent = round($jumps * $i++,0);
                      @endphp
                      <option percent="{{$percent}}" {{(isset($titulo) && $param->id == $titulo->estado) ? "selected" : ""}} value="{{$param->id}}">{{$percent}}% - {{$param->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                @foreach ($campos as $campo)
                  @php
                    $type = $campo->type == "Campo" ? "text" : "date";
                    $paramo = \App\valores_parametros::where("matricula",$matricula)->where("parametro_id",$campo->id)->first();
                    $value = $paramo ? $paramo->value : "";
                  @endphp
                  <div class="col-3">
                    <label for="">{{$campo->name}}</label>
                    <input class="form-control allow" name="{{"campo_$campo->id"}}" placeholder="{{$campo->name}}" type="{{$type}}" value="{{$value}}">
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
          <hr>
          @if (!$acta && !$certificado && !$titulo)
              <button type="submit" class="btn btn-success" name="button">
                Activar proceso
              </button>
            @else
              @if ($acta->avance == 100 &&
                $certificado->avance == 100 &&
                $titulo->avance == 100)
                  <label for="">Proceso de grado completado</label>
                @else
                  <div class="alert alert-warning">
                    <b>Para concluir el proceso, selecciona el 100% para cada estado y guarda.</b>
                  </div>
                  <hr>
                  <div class="clearix">
                    <div class="float-left">
                      <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                    <div class="float-right">
                      <a href="#" cid="{{base64_encode($matricula)}}" class="del">Eliminar</a>
                    </div>
                  </div>
              @endif
          @endif
        </div>
      </div>
    </div>
  </div>
  </form>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".acta").change(function(){
      let cval = $(this).find(":selected").attr("percent");
      $(".actacampo").val(cval);
      $(".actabarra").attr("aria-valuenow",cval);
      $(".actabarra").css("width",cval+"%");
      if(cval == 100){
        $(".actabarra").addClass("bg-success");
      } else {
        $(".actabarra").removeClass("bg-success");
      }
    });
    $(".certificado").change(function(){
      let cval = $(this).find(":selected").attr("percent");
      $(".certificadocampo").val(cval);
      $(".certificadobarra").attr("aria-valuenow",cval);
      $(".certificadobarra").css("width",cval+"%");
      if(cval == 100){
        $(".certificadobarra").addClass("bg-success");
      } else {
        $(".certificadobarra").removeClass("bg-success");
      }
    });
    $(".grado").change(function(){
      let cval = $(this).find(":selected").attr("percent");
      $(".gradocampo").val(cval);
      $(".gradobarra").attr("aria-valuenow",cval);
      $(".gradobarra").css("width",cval+"%");
      if(cval == 100){
        $(".gradobarra").addClass("bg-success");
      } else {
        $(".gradobarra").removeClass("bg-success");
      }
    });
    $(".del").bind("click",function(){
      event.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: '¿Deseas eliminar el proceso de grado?',
        text: "Los datos capturados se perderán definitivamente",
        showCancelButton: true,
        confirmButtonText: 'Continuar',
      }).then((result) => {
        if (result.isConfirmed) {
          ShowWaitNotifyTime("Eliminación en proceso");
          let e = $(this);
          $.post("/controlescolar/delprocess?matricula={{$matricula}}&cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
            ShowSuccessNotify("Proceso eliminado");
            location.reload();
          });
        }
      });
    });
  </script>
@endsection
