@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>RVOES</h3>
        <p>
          Edita las propiedades de un RVOE, además agregar, archiva, materias de la carrera
        </p>
      </div>
      <div class="float-right">
        <a href="/controlescolar/lista">Regresar</a>
      </div>
    </div>
    <hr>
    <div class="row">
      @php
        $revo = Request::get("cid") ? \App\revoes::whereRAW("md5(id)='".Request::get("cid")."'")->first() : null;
      @endphp
    </div>
    <form action="/revoes/{{$revo==null ? "guardar":"actualizar"}}" method="post">
      @csrf
      @if ($revo != NULL)
        <label for="">Folio</label>
        <div class="form-control">
          {{\Carbon\Carbon::parse($revo->created_at)->format("Y")}}{{$revo->id}}
        </div>
      @endif
      <label for="">Nombre</label>
      <input type="text" required name="nombre" value="{{$revo!=null?$revo->nombre:""}}" class="allow form-control" placeholder="ReVoe">
      <label for="">Clave</label>
      <input type="text" required name="clave" value="{{$revo!=null?$revo->clave:""}}" class="allow form-control" placeholder="65-334R">
      <label for="">Descripción</label>
      <input type="text" name="descr" value="{{$revo!=null?$revo->descr:""}}" class="allow form-control" placeholder="Descripción ">
      <br>
      <input type="submit" class="btn btn-primary" value="{{$revo!=null?"Actualizar":"Guardar"}}">
    </form>
  </div>
</div>
@if($revo != NULL)
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Materias</h3>
        @if (!Request::has("ar"))
            Tambien puedes ver <a href="/revoes/nuevo?cid={{Request::get("cid")}}&ar=true">materias archivadas</a>
          @else
            Tambien puedes ver <a href="/revoes/nuevo?cid={{Request::get("cid")}}">materias corrientes</a>
        @endif
      </div>
      <div class="float-right">
        <a href="/controlescolar/nuevamateria?cid={{Request::get("cid")}}" class="nuevo btn btn-primary btn-sm">Nueva materia</a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = !Request::has("ar") ? $revo->materias : $revo->materiasarchivadas;
        $menu = [
          "Nueva materia" => ["/controlescolar/nuevamateria?cid=".Request::get("cid"),"nuevo"]
        ];
      @endphp
      @if (count($datos) > 0)

      <table class="table pasarelas table-striped" id="materias" data-page-length='100'>
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
              Seriación
            </th>
            <th>
              Créditos
            </th>
            <th>
              Número
            </th>
            <th>
              Plan escolar
            </th>
            <th>
              Tipo de materia
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
              <td>
                <a href="/controlescolar/nuevamateria?cid={{Request::get("cid")}}&edit={{md5($mat->id)}}">
                  {{$mat->name}}
                </a>
              </td>
              <td>{{$mat->clave}}</td>
              <td>{{$mat->seriacion}}</td>
              <td>{{$mat->creditos}}</td>
              <td>{{$mat->numero}}</td>
              <td>{{$mat->planescolar->name}}</td>
              <td>{{$mat->tipodemateria->name}}</td>
              <td>
                <a href="#" cid="{{md5($mat->id)}}" class="del text-danger">
                  {{Request::has("ar") ? "Desa" : "A"}}rchivar
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
        <h4 class="text-center">
          Tambien puedes ver <a href="/revoes/nuevo?cid={{Request::get("cid")}}&ar=true">materias archivadas</a>
        </h4>
    @endif
    </div>
  </div>
</div>
@endif
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
        $("#materias").DataTable({
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
            title: '¿Deseas {{Request::has("ar") ? "desa" : "a"}}rchivar la materia?',
            text: "{{Request::has("ar") ? "Si eliminas una materia, esta quedará archivada en el sistema y no será mostrada en la lista principal" : "La materia será visible en la materias corrientes"}}",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              let e = $(this);
              ShowWaitNotify('Moviendo');
              $.post("/controlescolar/switchmat?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                console.log(e.parent().parent().remove());
                ShowSuccessNotify("Archivado");
              });
            }
          });

      });
  </script>
@endsection
