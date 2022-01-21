@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Pasarelas</h3>
    <hr>
    <div class="clearfix">
      <div class="float-left">
          <div class="col-8">
            Pasarelas para los diversos métodos de pago del sistema.
          </div>
      </div>
      <div class="float-right">
        <a href="/pagos/nuevapasarela" class="nuevo btn btn-primary">Nueva pasarela</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = \App\pasarelas::all();
        $menu = [
          "Nueva pasarela" => ["/pagos/nuevapasarela","nuevo"]
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
              Comisión
            </th>
            <th>
              Fijo
            </th>
            <th>
              IVA
            </th>
            <th>
              Forma de pago
            </th>
            <th>
              Acción
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $pas)
            <tr>
              <td>{{\Carbon\Carbon::parse($pas->created_at)->format("Y")}}{{$pas->id}}</td>
              <td>{{$pas->name}}</td>
              <td>{{$pas->comision}}</td>
              <td>{{$pas->fijo}}</td>
              <td>{{$pas->iva==1 ? "Aplica" : "No aplica"}}</td>
              <td>{{$pas->forma_pago}}</td>
              <td>
                <a href="#" cid="{{md5($pas->id)}}" class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay pasarelas
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
            title: '¿Deseas eliminar la pasarela?',
            text: "Si eliminadas la pasarela, los conceptos asignados ya no serán capaces de mostrar sus respectivos valores en la distribución.",
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
          }).then((result) => {
            if (result.isConfirmed) {
              $(this).find("i").removeClass("fa fa-trash");
              $(this).find("i").addClass("fas fa-cog fa-spin");
              let e = $(this);
              $.post("/pagos/delpas?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
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
