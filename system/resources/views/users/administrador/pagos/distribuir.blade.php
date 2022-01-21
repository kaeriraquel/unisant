@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $dist = \App\distribuciones::whereRAW("md5(id)='".Request::get("cid")."'")->first();
    $menu = [
      "Nuevo concepto" => [
        "/pagos/nuevadist?cid=".md5($dist->id),"nuevo"],
        "Regresar" => "/pagos/distribuciones",
      ];
  @endphp
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>Distribuir</h3>
        <small> {{$dist->distribucion}}</small>
      </div>
      <div class="float-right">
        <a href="/pagos/distribuciones">
          Regresar
        </a>
      </div>
    </div>
    <hr>
    <div class="">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $datos = $dist->conceptos;
      @endphp
      <div class="text-right">
        <a href="/pagos/nuevadist?cid={{md5($dist->id)}}" class="nuevo btn btn-primary text-right">Nuevo concepto de distribución</a>
      </div>
      <hr>
      @if (count($datos) > 0)
      <table class="table table2 facturas table-striped"  data-page-length='100'>
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Concepto
            </th>
            <th>
              Tipo
            </th>
            <th>
              Cantidad
            </th>
            <th>
              Acciones
            </th>
        </tr>
      </thead>
        <tbody>
          @php
            $porcentaje_t = 0.0;
            $monto_t = 0;
          @endphp
          @foreach ($datos as $div)
            @php
              if ($div->tipo == "Porcentaje sobre utilidad") {
                $porcentaje_t += $div->cantidad;
              } elseif(strstr($div->tipo,"Monto")){
                $monto_t += $div->cantidad;
              }
            @endphp
            <tr>
              <td>{{\Carbon\Carbon::parse($div->created_at)->format("Y")}}{{$div->id}}</td>
              <td>{{$div->concepto}}</td>
              <td>{{$div->tipo}}
                @if ($div->tipo == "Monto fijo por")
                  <b>{{$div->concepto_pago->concepto}}</b>
                @endif
              </td>
              <td>{{$div->cantidad}}</td>
              <td>
                <a href="#" cid="{{md5($div->id)}}" class="del text-danger">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <h3>Resultado esperado:</h3>
      <table class="table">
        <tr>
          <th>Variable</th>
          <th>Valor</th>
        </tr>
        <tr>
          <td>
            Monto total:
          </td>
          <td>
            {{$amount->format($monto_t)}}
          </td>
        </tr>
        <tr>
          <td>Porcentaje total:</td>
          <td>
            {{$porcentaje_t}}%
          </td>
        </tr>
      </table>
      @if ($porcentaje_t."" == 100)
        <div class="alert alert-success text-dark">
          <b>¡Genial!</b> La distribución es perfecta.
        </div>
      @endif
      @if ($porcentaje_t."" > 100)
        <div class="alert alert-warning text-dark">
          <b>¡Alerta!</b> La distribución se encuentra por encima del 100%.
        </div>
      @endif
      @if ($porcentaje_t."" < 100)
        <div class="alert alert-primary text-dark">
          <b>¡Ups!</b> La distribución se encuentra por debajo del 100%.
        </div>
      @endif
      @else
        <h3 class="text-center">
          <i class="fas fa-exclamation-triangle text-warning"></i> No hay conceptos de distribución
        </h3>
    @endif
    </div>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table2").DataTable(lang);
      $(".del").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas eliminar el concepto?',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(this).find("i").removeClass("fa fa-trash");
            $(this).find("i").addClass("fas fa-cog fa-spin");
            let e = $(this);
            $.post("/dist/delconcepto?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
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
