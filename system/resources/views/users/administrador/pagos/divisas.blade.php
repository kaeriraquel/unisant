@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Divisas de pago</h3>
    <hr>
    <div class="clearfix">
      <div class="float-left">
          <div class="col-8">
            Divisas con las cuales los pagos serán emitidos por las respectivas <a href="/sede/lista">sedes</a>.
          </div>
      </div>
      <div class="float-right">
        <a href="#" class="nuevo btn btn-primary">Nueva divisa</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = \App\divisas::all();
      @endphp
      @if (count($datos) > 0)
      <table class="table facturas table-striped"  data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Divisa
            </th>
            <th>
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $div)
            <tr>
              <td>{{\Carbon\Carbon::parse($div->created_at)->format("Y")}}{{$div->id}}</td>
              <td>{{$div->divisa}}</td>
              <td>
                <a href="#" cid={{md5($div->id)}} class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay divisas
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
          title: '¿Deseas eliminar la divisa seleccionado?',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).find("i").removeClass("fa fa-trash");
            $(this).find("i").addClass("fas fa-cog fa-spin");
            let e = $(this);
            $.post("/pagos/deldivisa?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
              console.log(e.parent().parent().remove());
            });
          }
        });
      });
      $(".nuevo").bind("click",function(){
        Swal.fire({
          title: 'Nueva divisa de sede',
          input: 'text',
          inputAttributes: {
            autocapitalize: 'off'
          },
          showCancelButton: true,
          confirmButtonText: 'Guardar',
          cancelButtonText: 'Cancelar',
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            return fetch('/pagos/nuevadivisa',
            {
              method:"POST",
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              body:JSON.stringify({
                divisa: $(".swal2-input").val(),
                 "_token": '{{csrf_token()}}'
               })
            })
              .then(response => {
                if (!response.ok) {
                  throw new Error(response.statusText)
                }
                return response.json()
              })
              .catch(error => {
                Swal.showValidationMessage(
                  `Request failed: ${error}`
                )
              })
          },
          allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          }
        })
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
