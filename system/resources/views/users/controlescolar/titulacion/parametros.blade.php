@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          <h4>Parámetros para el proceso de grado</h4>
        </div>
        <div class="float-right">

        </div>
      </div>
      <hr>
        <form action="/controlescolar/addparam" method="post">
          @csrf
          <div class="row">
            <div class="col">
              <label for="">Nombre del parámetro:</label>
              <input type="text" name="name" required class="form-control allow" placeholder="Nombre">
            </div>
            <div class="col">
              <label for="">Tipo de parámetro:</label>
              <select class="form-control allow" name="type" required>
                <option value="">Seleccione</option>
                <option value="Campo">Campo para capturar texto</option>
                <option value="CampoFecha">Campo para capturar fecha</option>
                <option value="Estado">Estado del proceso</option>
              </select>
            </div>
            <div class="col">
              <label for="">Proceso:</label>
              <select class="form-control allow" name="fora" required>
                <option value="">Seleccione</option>
                <option value="Acta">Acta</option>
                <option value="Certificado">Certificado</option>
                <option value="Grado">Grado</option>
              </select>
            </div>
          </div>
          <hr>
          <input type="submit" class="btn btn-primary" value="Agregar parámetro">
        </form>
      <hr>
    </div>
  </div>
  @php
    $campos = ["Acta","Certificado","Grado"];
    $inter = [
      "Campo" => "Texto",
      "CampoFecha" => "Fecha",
      "Estado" => "Estado"
    ];
  @endphp
  @foreach ($campos as $key => $v)
    <div class="card">
      <div class="card-body">
        <h4>Parámetros de {{$v}}</h4>
        <hr>
        <div class="row">
          <div class="col-6">
            <h6>Campos de captura</h6>
            <hr>
            <table class="table">
              @foreach (\App\parametros::whereIn("type",["Campo","CampoFecha"])->where("fora",$v)->where("sede_id",auth()->user()->sede->sede->id)->get()->sort() as $par)
                <tr>
                  <td>{{$par->id}}</td>
                  <td>{{$par->name}}</td>
                  <td>{{$inter[$par->type]}}</td>
                  <td>
                    <a cid="{{md5($par->id)}}" class="del" href="#">
                      Eliminar
                    </a>
                  </td>
                </tr>
              @endforeach
            </table>
          </div>
          <div class="col-6">
            <h6>Estados del proceso</h6>
            <hr>
            <table class="table">
              @foreach (\App\parametros::whereIn("type",["Estado"])->where("fora",$v)->where("sede_id",auth()->user()->sede->sede->id)->get() as $par)
                <tr>
                  <td>{{$par->id}}</td>
                  <td>{{$par->name}}</td>
                  <td>{{$inter[$par->type]}}</td>
                  <td>
                    <a cid="{{md5($par->id)}}" class="del" href="#">
                      Eliminar
                    </a>
                  </td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".del").bind("click",function(){
      event.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: '¿Deseas eliminar el parámetro?',
        text: "Eliminar un parámetro en uso puede afectar gravemente la consistencia de la información.",
        showCancelButton: true,
        confirmButtonText: 'Continuar',
      }).then((result) => {
        if (result.isConfirmed) {
          ShowWaitNotifyTime("Eliminación en proceso");
          let e = $(this);
          $.post("/controlescolar/delparam?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
            console.log(e.parent().parent().remove());
            ShowSuccessNotify("Parámetro eliminado");
          });
        }
      });
    });
  </script>
@endsection
