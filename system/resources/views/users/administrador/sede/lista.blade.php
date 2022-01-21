@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Sedes</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="/sede/nueva" class="btn btn-sm btn-primary">Nueva sede</a>
      </div>
    </div>
    <hr>
      <table class="table table-striped">
        <thead class=" text-primary">
          <tr>
          <th>
            Folio
          </th>
          <th>
            Nombre (API)
          </th>
          <th>
            Usuarios
          </th>
          <th>
            API
          </th>
          <th>
            Rv Estatales
          </th>
          <th>
            Planes de pago
          </th>
          <th>
            Conceptos de pago
          </th>
          <th>
            Folio actual
          </th>
          <th>
            Grupos
          </th>
          <th>
            Divisa
          </th>
          <th>
            Fecha de creación
          </th>
          <th class="text-right">
            Acciones
          </th>
        </tr></thead>
        <tbody>
            @foreach (\App\sedes::all() as $s)
              <tr>
                <td>
                  {{\Carbon\carbon::parse($s->created_at)->format("Y").$s->id}}
                </td>
                <td>
                  <div class="copy" to="https://siin.mx/api?set={{md5($s->sede)}}&_token={{md5($s->id)}}">
                    <i class="fas fa-clipboard"></i>
                    {{$s->sede}}
                  </div>
                </td>
                <td>
                  {{\App\sede_usuario::where("sede_id",$s->id)->count()}} usuarios
                </td>
                <td>
                  @if ($s->todos == NULL)
                    <span class="material-icons">
                    radio_button_unchecked
                    </span>
                    @else
                      <span class="material-icons">
                      task_alt
                      </span>
                  @endif
                  @if ($s->individual == NULL)
                    <span class="material-icons">
                    radio_button_unchecked
                    </span>
                    @else
                      <span class="material-icons">
                      task_alt
                      </span>
                  @endif
                </td>
                <td>
                  <a href="/sede/revoes?cid={{md5($s->id)}}">
                    {{count($s->revoes) > 0 ? "Cuenta con ".count($s->revoes) : "Sin Rv"}}
                  </a>
                </td>
                <td>
                  <a href="/sede/planes?cid={{md5($s->id)}}">
                    Planes ({{count($s->planespago)}})
                  </a>
                </td>
                <td>
                  <a href="/sede/conceptos?cid={{md5($s->id)}}">
                    Conceptos ({{count($s->conceptos)}})
                  </a>
                </td>
                <td>
                  {{$s->factura_count ?: "NA"}}
                </td>
                <td>
                  <a href="/sede/grupos?cid={{md5($s->id)}}">
                    Grupos
                  </a>
                </td>
                <td>
                  <a href="/sede/nueva?cid={{md5($s->id)}}">
                    {{$s->div ? $s->div->divisa : "MXN"}}
                  </a>
                </td>
                <td>
                  {{\Carbon\carbon::parse($s->created_at)->format("Y-m-d")}}
                </td>
                <td class="td-actions text-right">
                  {{-- <a rel="tooltip" class="btn btn-success btn-link" href="/sede/revoe?cid={{md5($s->id)}}" data-original-title="" title="">
                    <i class="material-icons">edit</i>
                    <div class="ripple-container"></div>
                  </a> --}}
                  <a rel="tooltip" class="btn btn-success btn-link" href="/sede/nueva?cid={{md5($s->id)}}" data-original-title="" title="">
                    Editar
                  </a>
                  <a class="del" cid="{{md5($s->id)}}" href="#">
                    Eliminar
                  </a>
                </td>
              </tr>
            @endforeach
        </tbody>
      </table>
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
      $(".table").DataTable({
          dom: 'Bfrtip',
          "language":lang.language,
          buttons: [
              'copy', 'csv', 'excel', 'pdf', 'print'
          ]
      });
      $(".table").on("draw.dt",function(){
        $(".del").bind("click",function(){
            Swal.fire({
              icon: 'warning',
              title: '¿Deseas eliminar la sede?',
              text: "Eliminar una sede puede afectar gravemente la consistencia de la información.",
              showCancelButton: true,
              confirmButtonText: 'Continuar',
            }).then((result) => {
              if (result.isConfirmed) {
                ShowWaitNotifyTime("Eliminación en proceso");
                let e = $(this);
                $.post("/sedes/del?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                  console.log(e.parent().parent().remove());
                  ShowSuccessNotify("Sede eliminada");
                });
              }
            });

        });
      });
      $(".copy").bind("click",function(){
        var aux = document.createElement("input");
        aux.setAttribute("value",$(this).attr("to"));
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
        Swal.fire({
            icon: 'success',
            title: '¡Copiado!',
            text: 'API copiado al portapapeles',
            footer: '<a target="_blank" href="'+$(this).attr("to")+'">¿Querías ver el JSON?</a>'
          })
      });
    });
  </script>
@endsection
