@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Conceptos de pago</h3>
    <hr>
    <div class="clearfix">
      <div class="float-left">
          <div class="col-8">
            Los conceptos de pago son elementos que permiten establecer una relación de los pagos ejercidos por los alumnos con los bienes o servicios emitidos.
          </div>
      </div>
      <div class="float-right">
        <a href="#" class="nuevo btn btn-primary">Nuevo concepto</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = \App\conceptospago::all();
      @endphp
      @if (count($datos) > 0)
      <table class="table facturas table-striped"  data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Concepto
            </th>
            <th>
              Multiplicidad de pagos
            </th>
            <th>
              Acciones
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $plan)
            <tr>
              <td>{{\Carbon\Carbon::parse($plan->created_at)->format("Y")}}{{$plan->id}}</td>
              <td>{{$plan->concepto}}</td>
              <td>
                  <a href="#" class="al" cid="{{md5($plan->id)}}">
                    {{$plan->allow == NULL ? "Permitir" : "Denegar"}}
                  </a>
              </td>
              <td>
                <a href="#" cid={{md5($plan->id)}} class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay conceptos de pago
        </h3>
    @endif
    </div>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table").DataTable(lang);
      $(".al").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Permitir/denegar multiples pagos de este concepto?',
          showCancelButton: true,
          confirmButtonText: 'Continuar',
        }).then((result) => {
          if (result.isConfirmed) {
            ShowWaitNotify("Cambiando");
            let e = $(this);
            $.post("/pagos/switchconcepto?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
              //console.log(e.parent().parent().remove());
              ShowSuccessNotify("Concepto cambiado");
              if(e.text() == "Permitir"){
                e.text("Denegar");
              } else {
                e.text("Permitir");
              }
            });
          }
        });
      });
      $(".del").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas eliminar el concepto seleccionado?',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).find("i").removeClass("fa fa-trash");
            $(this).find("i").addClass("fas fa-cog fa-spin");
            let e = $(this);
            $.post("/pagos/delconcepto?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
              console.log(e.parent().parent().remove());
            });
          }
        });
      });
      $(".nuevo").bind("click",function(){
        Swal.fire({
          title: 'Nuevo concepto de pago',
          input: 'text',
          inputAttributes: {
            autocapitalize: 'off'
          },
          showCancelButton: true,
          confirmButtonText: 'Guardar',
          cancelButtonText: 'Cancelar',
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            return fetch('/pagos/nuevoconcepto',
            {
              method:"POST",
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              body:JSON.stringify({
                concepto: $(".swal2-input").val(),
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
