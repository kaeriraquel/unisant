@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
          <h3>Tipos de materias</h3>
          @if (Request::has("ar"))
              <a href="/controlescolar/tiposdematerias">Tipos corrientes</a>
            @else
              <a href="/controlescolar/tiposdematerias?ar">Tipos archivados</a>
          @endif
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevotipo" class="nuevo btn btn-primary btn-sm">
          Nuevo tipo
        </a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = Request::has("ar") ? \App\tiposdematerias::where("deleted_at","<>",NULL)->get() : \App\tiposdematerias::where("deleted_at",NULL)->get();
        $menu = [
          "Nuevo tipo" => ["/controlescolar/nuevotipo","nuevo"]
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
              Tipo de materia
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
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay tipos de materias
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
            title: '¿Deseas {{Request::has("ar") ? "des" : ""}}archivar el tipo de materia?',
            text: "Sí {{Request::has("ar") ? "des" : ""}}archivas un tipo de materia, {{Request::has("ar") ? "" : "ya no"}} saldrá en la lista principal",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Moviendo');
              let e = $(this);
              $.post("/controlescolar/switchtiposdematerias?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
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
