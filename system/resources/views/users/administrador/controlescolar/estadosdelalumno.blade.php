@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
          <h3>Estados del alumno</h3>
          @if (Request::has("ar"))
              <a href="/controlescolar/estadosdelalumno">Estados corrientes</a>
            @else
              <a href="/controlescolar/estadosdelalumno?ar">Estados archivados</a>
          @endif
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevoestadodelalumno" class="nuevo btn btn-primary btn-sm">
          Nuevo estado
        </a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = Request::has("ar") ? \App\estadosdelalumno::where("deleted_at","<>",NULL)->get() : \App\estadosdelalumno::where("deleted_at",NULL)->get();
        $menu = [
          "Nuevo estado" => ["/controlescolar/nuevoestadodelalumno","nuevo"]
        ];
      @endphp
      @if (count($datos) > 0)
      <table class="table pasarelas table-striped" id="estados" data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Estado del alumno
            </th>
            <th>
              Defecto
            </th>
            <th>
              Fondo
            </th>
            <th>
              Texto
            </th>
            <th>
              Acciones
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $est)
            <tr>
              <td>{{\Carbon\Carbon::parse($est->created_at)->format("Y")}}{{$est->id}}</td>
              <td>{{$est->name}}</td>
              <td>
                <a href="#" cid="{{md5($est->id)}}" class="defecto">
                  {{$est->estado == NULL ? "Seleccionar" : "Por defecto"}}
                </a>
              </td>
              <td>
                <input type="color" name="background" cid={{md5($est->id)}} class="background" value="{{$est->background}}">
              </td>
              <td>
                <input type="color" name="color" cid={{md5($est->id)}} class="color" value="{{$est->color}}">
              </td>
              <td>
                <a href="#" cid="{{md5($est->id)}}" class="del text-danger">
                  {{Request::has("ar") ? "Desarchivar" : "Archivar"}}
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay estados del alumno
        </h3>
    @endif
    </div>
  </div>
</div>
@endsection
@section('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
@endsection
@section('scripts')
  <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
  <script type="text/javascript">
      $(function(){
        $("#estados").DataTable({
            dom: 'Bfrtip',
            "language":lang.language,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
      });

      $(".del").bind("click",function(){

          Swal.fire({
            icon: 'warning',
            title: '¿Deseas {{Request::has("ar") ? "des" : ""}}archivar el estado del alumno?',
            text: "Sí {{Request::has("ar") ? "des" : ""}}archivas un estado, {{Request::has("ar") ? "" : "ya no"}} saldrá en la lista principal",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Moviendo');
              let e = $(this);
              $.post("/controlescolar/switchestados?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                console.log(e.parent().parent().remove());
                ShowSuccessNotify("Archivado");
              });
            }
          });

      });
      $(".background").change(function(){
        ShowWaitNotify("Cambiando fondo de color");
        let e = $(this);
        let color = encodeURIComponent(e.val());
        $.post("/controlescolar/background?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}&val="+color,function(data){
          ShowSuccessNotify("Guardado");
        });
      });
      $(".color").change(function(){
        ShowWaitNotify("Cambiando texto de color");
        let e = $(this);
        let color = encodeURIComponent(e.val());
        $.post("/controlescolar/color?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}&val="+color,function(data){
          ShowSuccessNotify("Guardado");
        });
      });
      $(".defecto").bind("click",function(){

          Swal.fire({
            icon: 'warning',
            title: '¿Deseas seleccionar como estado por defecto?',
            text: "Sí marcas un elemento como estado por defecto, todos los demás estados serán desmarcados, solo puede haber uno a la vez.",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Haciendo estado por defecto');
              let e = $(this);
              $.post("/controlescolar/switchdefecto?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                $(".defecto").text("Seleccionar");
                e.text("Por defecto");
                ShowSuccessNotify("Estado por defecto listo!");
              });
            }
          });

      });
  </script>
@endsection
@section('styles')
  <style media="screen">
    .swal2-popup .swal2-input, .swal2-popup .swal2-file, .swal2-popup .swal2-textarea{
      width:80% !important
    }
  </style>
@endsection
