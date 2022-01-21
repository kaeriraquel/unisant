@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Distribuciones</h3>
    <hr>
    <div class="clearfix">
      <div class="float-left">
          <div class="col-8">
            Las distribuciones son elementos de apoyo para las conciliaciones de las sedes, distribuyen la utilidad en multiples entidades por <a href="/sede/lista">sede</a>.
          </div>
      </div>
      <div class="float-right">
        <a href="#" class="nuevo btn btn-primary">Nuevo grupo</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = \App\distribuciones::all();
        $menu = [
          "Nuevo grupo" => ["#","nuevo"]
        ];
      @endphp
      @if (count($datos) > 0)
      <table class="table facturas table-striped"  data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Distribución
            </th>
            <th>
              Distribuir
            </th>
            <th>
              Asignar
            </th>
            <th>
            </th>
        </tr>
      </thead>
        <tbody>
          @foreach ($datos as $div)
            <tr>
              <td>{{\Carbon\Carbon::parse($div->created_at)->format("Y")}}{{$div->id}}</td>
              <td>{{$div->distribucion}}</td>
              <td>
                <a href="/pagos/distribuir?cid={{md5($div->id)}}">
                  Distribuir
                </a>
              </td>
              <td>
                <a href="/pagos/asignardist?cid={{md5($div->id)}}">
                  ({{count($div->dist_grupos)}}) Asignar a grupos ...
                </a>
              </td>
              <td>
                <a href="#" cid="{{md5($div->id)}}" class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay distribuciones
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
        let con = {{count($div->dist_grupos)}};
        if(con > 0){
          Swal.fire({
            icon: 'warning',
            title: 'Primero elimina las sedes asignadas',
          })
        } else {
          Swal.fire({
            icon: 'warning',
            title: '¿Deseas eliminar la distribución seleccionado?',
            showCancelButton: true,
            confirmButtonText: 'Eliminar',
          }).then((result) => {
            if (result.isConfirmed) {
              $(this).find("i").removeClass("fa fa-trash");
              $(this).find("i").addClass("fas fa-cog fa-spin");
              let e = $(this);
              $.post("/pagos/deldistri?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                console.log(e.parent().parent().remove());
              });
            }
          });
        }
      });
      $(".nuevo").bind("click",function(){
        Swal.fire({
          title: 'Nueva distribución de sede',
          input: 'text',
          inputAttributes: {
            autocapitalize: 'off'
          },
          showCancelButton: true,
          confirmButtonText: 'Guardar',
          cancelButtonText: 'Cancelar',
          showLoaderOnConfirm: true,
          preConfirm: (login) => {
            return fetch('/pagos/nuevadistri',
            {
              method:"POST",
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              body:JSON.stringify({
                distri: $(".swal2-input").val(),
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
