@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
          <h3>Periodos</h3>
          @if (Request::has("ar"))
              <a href="/controlescolar/periodos">Periodos corrientes</a>
            @else
              <a href="/controlescolar/periodos?ar">Periodos archivados</a>
          @endif
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevoperiodo" class="nuevo btn btn-primary btn-sm">
          Nuevo periodo
        </a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = Request::has("ar") ? \App\periodos::where("deleted_at","<>",NULL)->get() : \App\periodos::where("deleted_at",NULL)->get();
        $menu = [
          "Nuevo periodo" => ["/controlescolar/nuevoperiodo","nuevo"]
        ];
      @endphp
      @if (count($datos) > 0)
      <table class="table pasarelas table-striped" id="periodos" data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Clave
            </th>
            <th>
              Nombre del periodo
            </th>
            <th>
              Sede
            </th>
            <th>
              Inicio
            </th>
            <th>
              Termino
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
              <td>{{$mat->clave}}</td>
              <td>{{$mat->periodo}}</td>
              <td>{{$mat->sede->sede}}</td>
              <td>{{\Carbon\Carbon::parse($mat->fecha_inicio)->format("d/m/Y")}}</td>
              <td>{{\Carbon\Carbon::parse($mat->fecha_termino)->format("d/m/Y")}}</td>
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
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay periodos
        </h3>
    @endif
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h3>Copiar periodos</h3>
    <form class="" action="/controlescolar/copiarperiodos" method="post">
      @csrf
      <label for="">Desde</label>
      <select class="form-control allow" required name="from_id">
        <option value="">Seleccionar</option>
        @foreach (\App\sedes::all() as $sede)
          <option value="{{$sede->id}}">{{$sede->sede}}</option>
        @endforeach
      </select>
      <label for="">Hacia</label>
      <select class="form-control allow" required name="to_id">
        <option value="">Seleccionar</option>
        @foreach (\App\sedes::all() as $sede)
          <option value="{{$sede->id}}">{{$sede->sede}}</option>
        @endforeach
      </select>
      <hr>
      <input type="submit" class="btn btn-primary" value="Copiar">
    </form>
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
        $("#periodos").DataTable({
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
            title: '¿Deseas {{Request::has("ar") ? "des" : ""}}archivar el periodo?',
            text: "Sí {{Request::has("ar") ? "des" : ""}}archivas un periodo, {{Request::has("ar") ? "" : "ya no"}} saldrá en la lista principal",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              ShowWaitNotify('Moviendo');
              let e = $(this);
              $.post("/controlescolar/switchperiodos?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
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
