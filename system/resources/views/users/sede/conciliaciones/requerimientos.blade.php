@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="card">
    <div class="card-body">
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $c = \App\conciliaciones::whereRAW("md5(id)='".Request::get("cid")."'")->first();
        $total_p = 0;
        $total_r = 0;
        $c3 = 0;
        foreach ($c->pagos as $_p) {
          $total_p += $_p->monto;
          $c3++;
        }
        foreach ($c->requerimientos as $_r) {
          $total_r += $_r->monto;
        }
      @endphp
      <h3>
        {!!!$c->estado ? '<i class="far fa-clock text-warning"></i>' : '<i class="fas fa-check-circle text-success"></i>'!!}
        Conciliación - <small>{{$c->concepto ?: "Sin concepto"}}</small>
      </h3>
      @if ($c->nota != NULL)
        <div style="width:200px;position:absolute;right:10px;top:-50px;">
          <div class="alert alert-warning pa">
              <div class="clearfix">
                <div class="float-left">
                  <i class="fas fa-thumbtack text-light"></i> <b style="margin-left:10px;">Nota</b>
                </div>
                <div class="float-right">
                  <a href="javascript:$('.pa').fadeOut();">X</a>
                </div>
              </div>
              <br>
              <p align="justify">
                {{$c->nota}}
              </p>
          </div>
        </div>
      @endif
      <b>{{$c->sede->sede}} {!!!$c->estado ? "(En espera)" : '(Conciliado el '.$c->updated_at.')'!!}</b></br>
      Creado el: {{\Carbon\Carbon::parse($c->created_at)->format("Y-M-d")}}</br>
      <small>Desde: {{$c->desde}} al: {{$c->hasta}}</small>
      <hr>
      <div class="row">
        <div class="col-4 text-center">
          <h3 class="text-success">
            {{$amount->format($total_p)}}
          </h3>
          <h6>RECAUDACIÓN TOTAL</h6>
        </div>
        <div class="col-8">
          <br>
          <div class="row">
            <div class="col text-center">
              <h4 class="text-success">
                {{count($c->pagos)}}
              </h4>
              <h6>Pagos</h6>
            </div>
            <div class="col text-center">
              <h4 class="text-success">
                {{count($c->requerimientos)}}
              </h4>
              <h6>Requerimientos</h6>
            </div>
          </div>
        </div>
      </div>
      @if ($c->estado != NULL)
        <hr>
        <div class="row">
          <div class="col">
            <table class="conceptos table table-striped">
              <thead>
                <th>Distribución</th>
                <th>Monto</th>
              </thead>
              <tbody>

                @foreach ($c->conceptos as $con)
                  @if ($con->show_sede != NULL)
                    @php
                      $valsum = str_replace("$","",str_replace(",","",$con->value));
                      $some = $valsum <= 0 ? "danger" : "dark";
                    @endphp
                    <tr>
                      <td>{{$con->keyval}}</td>
                      <td class="text-{{$some}}">{{$con->value}}</td>
                    </tr>
                  @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
      <hr>
      <div class="clearfix">
        <div class="float-left">
          <h4>Pagos de materias incluidos</h4>
        </div>
        <div class="float-right">
          @if ($c->estado == null)
            <form action="/conciliaciones/deshacer" method="post">
              @csrf
              <input type="hidden" name="cid" value="{{md5($c->id)}}">
              <button type="submit" class="btn btn-primary btn-sm">Deshacer</button>
            </form>
          @endif
        </div>
      </div>
      <hr>
      <table class="table table-striped" id="materias" data-page-length="200">
        <thead>
          <th>Folio</th>
          <th>Monto</th>
          <th>Alumno</th>
          <th>Matricula</th>
          <th>Fecha de pago</th>
          <th>Comprobante</th>
          <th>Tipo de pago</th>
          <th>Concepto</th>
        </thead>
        <tbody>
        @foreach ($c->pagos as $pago)
          <tr>
            <td>
              {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
            </td>
            <td>
              {{$amount->format($pago->monto)}}
            </td>
            <td>
              @php
                $nombre = \App\nombres::where("matricula",$pago->matricula)->first();
              @endphp
              <a class="text-danger" href="/alumnos/pagos?cid={{base64_encode($pago->matricula)}}&did={{$c->sede->id}}&pago={{md5($pago->id)}}">
                @if (Request::has("pago") && Request::get("pago") == md5($pago->id))
                  <i class="fas fa-arrow-circle-right text-success"></i>
                @endif
                @if (session()->has("debug"))
                  {{$pago->matricula}}
                  @else
                    @if(isset($nombre))
                      {{$nombre->nombre}}
                    @else
                      @php
                        $a = \App\alumnosest::where("matricula",$pago->matricula)->first();
                      @endphp
                      @if ($a != null)
                          {{$a->nombre_completo." ".$a->apat." ".$a->amat}}
                        @else
                          Sin nombre ({{$pago->matricula}})
                      @endif
                    @endif
                @endif
              </a>
            </td>
            <td>
              {{\App\grupos::where("matricula",$pago->matricula)->first()->grupo}}
            </td>
            <td>
              {{\Carbon\carbon::parse(($pago->fecha_pago ?: NULL))->format("Y-M-d")}}
            </td>
            <td>
              {!!($pago->document_id == 0 ?
                 "Sin comprobante" :
                  "<a target='_blank' href='/ver/".md5($pago->document_id)."'>".'<i class="fas fa-image"></i>'."</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>".'<i class="fas fa-download"></i>'."</a>")!!}
            </td>
            <td>
              {{$pago->clave}}({{$pago->pasarela_id == NULL ? "Default" : $pago->pasarela->name}})
            </td>
            <td>
              {{$pago->concepto ?: "Sin concepto"}}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <hr>
    @if (count($c->requerimientos) > 0)
    <h4>Requerimientos incluidos</h4>
    <hr>
    <table class="table table-striped requerimientos">
      <thead>
        <th>Monto</th>
        <th>Concepto</th>
        <th>Adjuntos</th>
        <th></th>
      </thead>
      <tbody>
      @foreach ($c->requerimientos as $req)
        <tr>
          <td>{{$amount->format($req->monto)}}</td>
          <td>{{$req->con->concepto}}</td>
          <td>
            {!!($req->document_id == 0 ? "Sin adjunto" : "<a target='_blank' href='/ver/".md5($req->document_id)."'>Ver adjunto</a> / <a target='_blank' href='/descargar/".md5($req->document_id)."'>Descargar</a>")!!}
          </td>
          <td class="text-right">
            @if ($c->estado == NULL)
              <form action="/conciliaciones/eliminarreq" method="post">
                @csrf
                <input type="hidden" name="cid" value="{{md5($req->id)}}">
                <button type="submit" style="border:0;margin:0;padding:0;background:none;" class="text-danger"><i class="fa fa-trash"></i> Eliminar</button>
              </form>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
    @endif

    @if ($c->estado == NULL)
      <h3>Añadir nuevo requerimiento</h3>
      <hr>
      <form class="" action="/conciliaciones/requerimiento" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="cid" value="{{md5($c->id)}}">
        <label class="text-dark" for="">Concepto</label>
        <select class="form-control" name="concepto">
          @foreach (\App\conceptos::where("activo","1")->where("usos","<>",-1)->get() as $con)
            <option value="{{$con->id}}">{{$con->concepto}}</option>
          @endforeach
        </select>
        <label class="text-dark" for="">Monto</label>
        <input required type="number" class="form-control" name="monto" placeholder="0.00" value="">
        <label class="text-dark" for="">Documento adjunto:</label>
        <input type="file" class="form-control" name="file" value="">
        <hr>
        <input type="submit" class="btn btn-primary" value="Añadir requerimiento">
      </form>
    @endif
  </div>
</div>
@endsection
@section('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">
@endsection
@section('scripts')
  @if ($c->estado != NULL)
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
  @endif
  <script type="text/javascript">
  $(".conceptos").DataTable({
      dom: 'Bfrtip',
      "language":lang.language,
      buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
      ]
  });
  $("#materias").DataTable({
      dom: 'Bfrtip',
      "language":lang.language,
      buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
      ]
  });
  $(".requerimientos").DataTable({
      dom: 'Bfrtip',
      "language":lang.language,
      buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
      ]
  });
  </script>
@endsection
