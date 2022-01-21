@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Materias</h3>
    <hr>
    <div class="clearfix">
      <div class="float-left">
          <div class="col-12">
            Materias que pueden cursar los alumnos
          </div>
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevamateria" class="nuevo btn btn-primary">Nueva materia</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = \App\materias::all();
        $menu = [
          "Nueva materia" => ["/controlescolar/nuevamateria","nuevo"]
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
          @foreach ($datos as $mat)
            <tr>
              <td>{{\Carbon\Carbon::parse($mat->created_at)->format("Y")}}{{$mat->id}}</td>
              <td>{{$mat->name}}</td>
              <td>{{$mat->clave}}</td>
              <td>
                <a href="#" cid="{{md5($mat->id)}}" class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay materias
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
            title: '¿Deseas archivar la materia?',
            text: "Si eliminas una materia, esta quedará archivada en el sistema y no será mostrada en la lista principal",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Moviendo');
              let e = $(this);
              $.post("/controlescolar/delmat?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
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
