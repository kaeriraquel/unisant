@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])
@section('content2')
  @php
    $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
  @endphp
<form class="con" action="/conciliaciones/nueva" method="post">
  @csrf
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body" id="conciliacion">
          <div class="clearfix">
            <div class="float-left">
              <h3>Nueva conciliación</h3>
            </div>
            <div class="float-right">
              <br>
              <a href="/">Regresar</a>
            </div>
          </div>
          <hr>
          <h6>Lista de pagos pendientes</h6>
          <div class="">
            Selecciona todos los pagos que deseas conciliar, una vez que selecciones los pagos para conciliación, los pagos serán marcados como "En revisión" y no podrán ser modificados.
          </div>
          </div>
          <div class="card-body" style="background-color:#E0F2F1;">
            <h6>Información de conciliación</h6>
            <label for="" class="text-dark">Nombre:</label>
            <input type="text" class="conceptocon form-control allow" name="concepto" required placeholder="Concepto de conciliación">
            <label for="" class="text-dark">Nota(Opcional):</label>
            <textarea name="nota" class="form-control allow" style="height:50px;" placeholder="Nota (Opcional)"></textarea>
            <div class="row">
              <div class="col">
                <label for="" class="text-dark">Del</label>
                <input type="date" class="form-control allow desde" required name="desde" value="">
              </div>
              <div class="col">
                <label for="" class="text-dark">Al</label>
                <input type="date" class="form-control allow hasta" required name="hasta" value="">
              </div>
            </div>
            <br>
          </div>
          <div class="card-body">
          <h6>Filtros de busqueda</h6>
          <div class="row">
            <div class="col-4">
              <label for="" class="text-dark">Desde</label>
              <input type="date" class="form-control allow" id="min">
            </div>
            <div class="col-4">
              <label for="" class="text-dark">Hasta</label>
              <input type="date" value="{{date("d/m/Y")}}" class="form-control allow" id="max">
            </div>
            <div class="col">
              <br>
              <button class="btn btn-primary clear" type="button" name="button">
                Limpiar
              </button>
            </div>
          </div>
          <hr>
          <table data-page-length="10000" class="table pagos table-striped">
            <thead>
              <th>
                <input type="checkbox" class="elcheck" id="elcheck" value="true">
              </th>
              <th>Folio</th>
              <th>Monto</th>
              <th>Pagos</th>
              <th>Cargos</th>
              <th>Nombre</th>
              <th>Fecha de pago</th>
              <th>Grupo</th>
              <th>Comprobante</th>
              <th>Tipo de pago</th>
            </thead>
            <tbody>
              @php
              $ps = \App\pagos::whereHas("sede",function($q){
                $q->where("sede_id",auth()->user()->sede->sede->id);
              })->where("estado",NULL)->where("deleted_at",NULL)->where("returned_at",NULL)->get();

              @endphp
              @foreach ($ps as $pago)
                <tr>
                  <td>
                    @if ($pago->plan_pagos)
                      <input type="checkbox" class="pagos" name="pagos[]" id="check_{{$pago->id}}" value="{{$pago->id}}">
                      @else
                        <a href="/alumnos/planes?cid={{base64_encode($pago->matricula)}}">
                          Sin plan o fechas
                        </a>
                    @endif
                  </td>
                  <td>
                    {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                  </td>
                  <td>
                    {{$amount->format($pago->monto)}}
                  </td>
                  <td>
                      {{$pago->cantidad_pagos == NULL ? "1" : $pago->cantidad_pagos}}
                  </td>
                  <td>
                    @if ($pago->extra == NULL)
                      Sin cargos
                      @else
                        <div class="fw-bold text-{{$pago->extra > 0 ? "success" : "danger"}}">
                          {{$pago->metodo.$pago->extra}}
                        </div>
                    @endif
                  </td>
                  <td>
                    @php
                      $nombre = \App\nombres::where("matricula",$pago->matricula)->first();
                    @endphp
                    <a class="text-danger" href="/alumnos/pagos?cid={{base64_encode($pago->matricula)}}">
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
                    </a>
                  </td>
                  <td>
                    {{\Carbon\carbon::parse(($pago->fecha_pago ?: NULL))->format("Y-M-d")}}
                  </td>
                  <td>
                    {{\App\grupos::where("matricula",$pago->matricula)->first()->grupo}}
                  </td>
                  <td>
                    {!!($pago->document_id == 0 ? "Sin comprobante" : "<a target='_blank' href='/ver/".md5($pago->document_id)."'>Ver comprobante</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>Descargar</a>")!!}
                  </td>
                  <td>
                    {{$pago->clave}}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <hr>
          <div class="clearfix">
            <div class="float-left">
              <input type="button" class="btn btn-primary crear" value="Crear conciliación">
            </div>
            <div class="float-right">
              <a class="btn btn-link text-danger" href="/">Cancelar</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
  <script type="text/javascript" src="//cdn.datatables.net/plug-ins/1.11.3/dataRender/datetime.js"></script>
  <script type="text/javascript">
      $(".elcheck").bind("click",function(){
        $("input[type='checkbox']").prop('checked', $(this).is(':checked'));
      });
      $(".crear").bind("click",function(){
        if($(".pagos:checked").length > 0 && $(".conceptocon").val() != "" && $(".desde").val()!= "" && $(".hasta").val() != ""){
          $(".con").submit();
        } else {
          location.href = "#conciliacion";
          Swal.fire(
            '¡Ups!',
            'Debes de seleccionar al menos un pago, escribir un concepto de conciliación, y, seleccionar las fechas de interacción.',
            'warning'
          )
        }
      });
      var minDate, maxDate;

      $(document).ready(function() {
        var table = $('.table').DataTable();

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var min = new Date($("#min").val()+" 00:00:00");
                var max = new Date($("#max").val()+" 00:00:00");
                var date = new Date(data[6]);
                console.log(min+"-"+date+"-"+max)
                if(min == "Invalid Date" && max == "Invalid Date")
                  return true;
                if(date >= min && max == "Invalid Date")
                  return true;
                if(min == "Invalid Date" && max >= date)
                  return true
                if(min <= date && max >= date)
                  return true;

                return false;
            }
        );

        $('#min, #max').bind("blur",function(){
          table.draw();
          $(".elcheck").prop('checked',false);
        })
        $(".clear").bind("click",function(){
          $("#min, #max").val("");
          table.draw();
          $(".elcheck").prop('checked',false);
        })

      });
  </script>
@endsection
