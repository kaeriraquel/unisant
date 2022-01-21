@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Carreras</h3>
    <hr>
    <div class="clearfix">
      <div class="float-left">
          <div class="col-12">
            Las carreras son elementos que sirven para agrupar materias a fin de crear una especialidad
          </div>
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevacarrera" class="nuevo btn btn-primary">Nueva carrera</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = \App\carreras::all();
        $menu = [
          "Nueva v" => ["/controlescolar/nuevacarrera","nuevo"]
        ];
      @endphp
      @if (count($datos) > 0)
      <table class="table pasarelas table-striped"  data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Nombre
            </th>
            <th>
              Clave
            </th>
            <th>
              Acción
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $car)
            <tr>
              <td>{{\Carbon\Carbon::parse($car->created_at)->forcar("Y")}}{{$car->id}}</td>
              <td>{{$car->name}}</td>
              <td>{{$car->clave}}</td>
              <td>
                <a href="#" cid="{{md5($car->id)}}" class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay carreras
        </h3>
    @endif
    </div>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table").DataTable(lang);
      $(".del").bind("click",function(){

          Swal.fire({
            icon: 'warning',
            title: '¿Deseas archivar la carrera?',
            text: "Si eliminas una carrera, esta quedará archivada en el sistema y no será mostrada en la lista principal",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              $(this).find("i").removeClass("fa fa-trash");
              $(this).find("i").addClass("fas fa-cog fa-spin");
              let e = $(this);
              $.post("/controlescolar/delcar?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                console.log(e.parent().parent().remove());
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
