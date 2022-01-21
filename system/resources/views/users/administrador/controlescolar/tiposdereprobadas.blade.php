@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
          <h3>Tipos de materias reprobadas</h3>
          @if (Request::has("ar"))
              <a href="/controlescolar/tiposdereprobadas">Tipos corrientes</a>
            @else
              <a href="/controlescolar/tiposdereprobadas?ar">Tipos archivados</a>
          @endif
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevotiporeprobada" class="nuevo btn btn-primary btn-sm">
          Nuevo tipo
        </a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = Request::has("ar") ? \App\tiposdereprobadas::where("deleted_at","<>",NULL)->get() : \App\tiposdereprobadas::where("deleted_at",NULL)->get();
        $menu = [
          "Nuevo tipo" => ["/controlescolar/nuevotiporeprobada","nuevo"]
        ];
      @endphp
      @if (count($datos) > 0)
      <table class="table pasarelas table-striped" id="tipos" data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Tipo de materia reprobada
            </th>
            <th>
              Acciones
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $mat)
            <tr>
              <td>{{\Carbon\Carbon::parse($mat->created_at)->format("Y")}}{{$mat->id}}</td>
              <td>{{$mat->name}}</td>
              <td>
                <a href="#" cid="{{md5($mat->id)}}" class="del text-danger">
                  {{Request::has("ar") ? "Desarchivar" : "Archivar"}}
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay tipos de materias reprobadas
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
        $("#tipos").DataTable({
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
            title: '¿Deseas {{Request::has("ar") ? "des" : ""}}archivar el tipo de materia reprobadas?',
            text: "Sí {{Request::has("ar") ? "des" : ""}}archivas un tipo de materia, {{Request::has("ar") ? "" : "ya no"}} saldrá en la lista principal",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Moviendo');
              let e = $(this);
              $.post("/controlescolar/switchtiposdereprobadas?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                console.log(e.parent().parent().remove());
                ShowSuccessNotify("Archivado");
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
