@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $menu = [
      "Regresar" => "/alumnos/pagos?cid=".urlencode(Request::get('cid')),
    ];
    $i = 1;
    $planes_sedes = [];
    $planes = \App\alumnos_planes::where("matricula",base64_decode(Request::get("cid")))->get();
    $planes = $planes->sortDesc(0);
  @endphp
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          <h3>Planes de pago</h3>
          <p>Añadir un nuevo plan de pagos creará un esquema de pagos a medida para el alumno, los pagos que coincidan con el concepto, serán vinculados automáticamente a los plazos del plan.</p>
        </div>
        <div class="float-right">
          <a href="/alumnos/pagos?cid={{Request::get("cid")}}">Regresar</a>
        </div>
      </div>
      <hr>
      @php
      $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
      @endphp
      <form action="/alumnos/addplanes" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col">
            @csrf
            <input type="hidden" name="cid" value="{{Request::get("cid")}}">
            <label for="">Añadir nuevo plan de pagos:</label>
            <select class="allow form-control" required name="plan_id" style="padding-left:10px;">
              <option value="">Seleccion plan de pagos</option>
              @if (\Auth::user()->nivel->name == "Administrador")
                  @foreach (\App\planespago::all() as $plan)
                    <option value="{{$plan->id}}">{{$plan->concepto}} Monto: {{$plan->monto}} Plazos: {{$plan->plazo}}</option>
                  @endforeach
                @else
                  @foreach (\Auth::user()->sede->sede->planespago as $plan_sede)
                    @if (!$plan_sede->plan->disable)
                      <option value="{{$plan_sede->plan->id}}">{{$plan_sede->plan->concepto}} Monto: {{$amount->format($plan_sede->plan->monto)}} Plazos: {{$plan_sede->plan->plazo}}</option>
                    @endif
                  @endforeach
              @endif
            </select>
          </div>
          <div class="col">
            <br>
            <input class="btn btn-primary" type="submit" name="" value="Añadir plan">
            <a href="#" class="nplan btn">¿No ves el plan adecuado?, Elabora uno</a>
          </div>
        </div>
      </form>
      </div>
    </div>

<div class="nuevoplan" style="display:none">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <div class="clearfix">
            <div class="float-left">
              <h3>Nuevo plan de pagos</h3>
              <p>Considera que:
                <ul>
                  <li>
                    El plan de pagos individual no estará disponible para agregar posteriormente, es un plan individual por alumno.
                  </li>
                  <li>
                    El concepto del plan de pago debe ser el concepto con el que serán registrados los pagos a reflejarse en el plan, normalmente: colegiaturas, cuotas, etc.
                  </li>
                  <li>
                    No puedes modificar el nombre del plan de pagos.
                  </li>
                  <li>
                    Sí el plan de pagos no es adecuado, puede ser retirado por un administrador sin el concentimiento de la sede.
                  </li>
                  <li>
                    Los planes de pago individuales solo deben ser generados si se considera que es un caso especial y no es apto para crear un plan genérico.
                  </li>
                  <li>
                    Los planes de pago individual pueden utilizarse como complemento a pagos detenidos o restructurados.
                  </li>
                </ul>
              </p>
            </div>
            <div class="float-right">
              <a href="#" class="nplan">
                <i class="fas fa-times"></i>
              </a>
            </div>
          </div>
          <hr>
          <form action="/alumnos/addcustomplan" method="post">
            @csrf
            <input type="hidden" name="cid" value="{{Request::get("cid")}}">
            <div class="row">
              <div class="col-4">
                <label for="">Nombre del plan</label>
                @php
                  $alumno = \App\alumnosest::where("matricula",base64_decode(Request::get("cid")))->first();
                  if($alumno == null){
                    $nombres = \App\nombres::where("matricula",(base64_decode(Request::get("cid"))))->first();
                    $nombre = $nombres->nombre ? $nombres->nombre : "$nombres->nombre_completo $nombres->apat $nombres->amat";
                  } else {
                    $nombre =  "$alumno->nombre_completo $alumno->apat $alumno->amat";
                  }

                @endphp
                <input type="hidden" name="concepto" value="Plan local: {{$nombre}}">
                <input class="form-control" disabled type="text" required value="Plan local: {{$nombre}}">
              </div>
              <div class="col-4">
                <label for="">Monto total</label>
                <input class="form-control allow" min="1" type="number" step="0.01" name="monto" required placeholder="60800">
              </div>
              <div class="col-4">
                <label for="">Plazos</label>
                <input class="form-control allow" min="1" type="number" step="1" name="plazo" required placeholder="40">
              </div>
              <div class="col-4">
                <label for="">Concepto</label>
                <select class="form-control allow" required name="concepto_id">
                  <option value="">Seleccione</option>
                  @foreach (Auth::user()->sede->sede->conceptos as $con)
                    @if (isset($con->concepto))
                      <option cid="{{$con->concepto->id}}" value="{{$con->concepto->id}}">{{$con->concepto->concepto}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <div class="row">
            <div class="col">
              <br>
              <hr>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-wallet"></i> Crear plan de pago
              </button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col">
    @if (count($planes) > 0)
        @foreach ($planes as $plan_sede)
          @php
            $pl = $plan_sede->plan;
            $monto_recaudado = 0;
            $unpago = 0;
          @endphp
          <div class="card">
            <div class="card-header">
              <div class="clearfix">
                <div class="float-left">
                  <br><b>
                  @if ($pl != null)
                      @php
                        foreach ($plan_sede->planes_pagos as $pp) {
                          if($pp->pago != null)
                            {
                              $monto_recaudado += $pp->pago->monto;
                            }
                        }
                        $contador_pagos = 0;
                        $fecha = \Carbon\carbon::today()->format("Y-m-d");
                      @endphp
                      <div class="titulo">
                        <h4>{{$i++}}. {{$plan_sede->plan->concepto}} de {{$amount->format($plan_sede->plan->monto)}} a {{$plan_sede->plan->plazo}} {{$plan_sede->plan->plazo == 1 ? "plazo" : "plazos"}} relacionado a {{$plan_sede->plan->conceptopago->concepto}}</h4>
                        Folio:{{\Carbon\carbon::parse($plan_sede->plan->created_at)->format("Y")}}{{$plan_sede->id}}
                      </div>
                      <div class="desc d-none">
                        <h3>
                          <b>Estado de cuenta</b>
                        </h3>
                        <b>Matrícula: </b> {{$plan_sede->alumno ? $plan_sede->alumno->matricula : base64_decode(Request::get("cid"))}}
                        <b style="margin-left:20px;">Ciclo: </b> {{$alumno ? \App\periodos::find($alumno->periodo_id)->first()->periodo : "No definido"}}
                        </br>
                        <b>Nombre: </b> {{$nombre}}</br>
                        <b>Folio: </b>{{\Carbon\carbon::parse($plan_sede->plan->created_at)->format("Y")}}{{$plan_sede->id}}</br>
                        <b>Fecha de emisión: </b>{{$fecha ?? ''}}</br>
                      </div>
                    @else
                      <h4>Sin definir (NULL)</h4>
                  @endif
                </b>
                </div>
                <div class="float-right">
                  <div class="logoa d-none">
                    <img id="logoa" src="/images/logo.png" style="position:relative;width:300px;margin-right:20px;margin-top:50px;" alt="">
                  </div>
                  <div class="">
                    <a class="del{{$monto_recaudado > 0 ? "a" : ""}}" cid="{{md5($plan_sede->id)}}" href="#">
                      Eliminar
                    </a>
                    <a class="print" href="#">
                      Imprimir
                    </a>
                  </div>
                </div>
              </div>
            </div>
            @if ($pl != null)
            <div class="card-body" id="tb_Logbook{{md5($plan_sede->id)}}">
                <div class="row">
                  <div class="col">
                    @if ($plan_sede->since == NULL && $plan_sede->every == NULL)
                      <div class="row">
                        <div class="col">
                          <div class="alert alert-warning">
                            <b>Plan sin fechas, agrega un fecha de inicio y una periodicidad de cobro.</b>
                          </div>
                        </div>
                      </div>
                    @endif
                    <table class="table table-striped">
                      <tr>
                        <td>Total recibido:</td>
                        <td>
                          <b>{{$amount->format($monto_recaudado)}}</b>
                        </td>
                        <td>Saldo:</td>
                        <td>
                          <b class="saldo_{{$plan_sede->id}}">$0.00</b>
                        </td>
                        <td>
                          Fecha de inicio:
                        </td>
                        <td>
                          @if ($plan_sede->since != NULL)
                              <div class="editable" cid="{{md5($plan_sede->id)}}">
                                <b class="fecha">
                                  {{$plan_sede->since}}
                                </b>
                              </div>
                            @else
                              <input class="allow form-control since-{{md5($plan_sede->id)}}" type="date" name="since" >
                          @endif
                        </td>
                        <td><a href="#" class="question"><i class="fas fa-question-circle"></i> Cada:</a> </td>
                        <td>
                          @if ($plan_sede->since != NULL)
                              <div class="editableday" cid="{{md5($plan_sede->id)}}">
                              <b class="day" dias="{{$plan_sede->every}}">{{$plan_sede->every < 31 ? "$plan_sede->every días" : "Mes"}}</b>
                              </div>
                            @else
                              <input type="number" style="width:60px;" step="1" min="1" max="31" class="allow form-control every-{{md5($plan_sede->id)}}" name="every" value="31">
                              <span class="dias"></span>
                          @endif
                        </td>
                        <td class="panelbeca">
                          <a href="#" class="bq"><i class="fas fa-question-circle"></i> Beca:</a>
                        </td>
                        <td class="panelbeca">
                          <div class="editablebeca" cid="{{md5($plan_sede->id)}}">
                            <b class="beca" beca="{{$plan_sede->beca}}">{{$plan_sede->beca}}%</b>
                          </div>
                        </td>
                        @if ($plan_sede->since == NULL && $plan_sede->every == NULL)
                          <td class="text-right ">
                              <a href="#" cid="{{md5($plan_sede->id)}}" class="actualizar btn btn-primary">
                                <i class="fas fa-redo"></i>
                                Actualizar
                              </a>
                          </td>
                          @else
                            @if ($plan_sede->disable == null)
                              <td class="text-right ">
                                  <a href="#" cid="{{md5($plan_sede->id)}}" class="detener btn btn-primary">
                                    <i class="far fa-stop-circle"></i>
                                    Detener plan
                                  </a>
                              </td>
                              @else
                                <td class="text-center">
                                  <i class="fas fa-hourglass-half"></i>
                                  Plan detenido
                                </td>
                            @endif
                        @endif
                      </tr>
                    </table>
                  </div>
                </div>
                <table class="table table-striped plan_sede_{{$plan_sede->id}}">
                  @php
                    $since = $plan_sede->since;
                    $every = $plan_sede->every;
                    $desde = \Carbon\carbon::parse($since);

                    if($since != NULL && $every != NULL){
                      $_fechapago = null;
                      if($every <= 30){
                        $desde->subDays($every);
                      } elseif($every == 31){
                        $desde->subMonth(1);
                      }
                    }

                    $planes_sedes[$plan_sede->id] = $plan_sede->id;
                    $_p = [];
                    foreach ($plan_sede->planes_pagos as $pp) {
                      $_p[$pp->pago->id] = $pp->pago->monto;
                    }
                    $saldo_pendiente = 0;
                  @endphp
                  <tbody>
                    @for ($o = 1; $o <= $pl->plazo;$o++)
                      @php
                        $success = "bg-success";
                        $abonado = "bg-warning";
                        $ours = [];
                        $ours_id = [];
                        $none = $plan_sede->disable == null ? "" : "d-none";
                        $monto_ = $pl->monto/$pl->plazo;
                        $monto = $pl->monto/$pl->plazo;
                        $status = "";

                        if($monto <= $monto_recaudado){
                          $status = $success;
                          $contador_pagos++;
                          $monto_recaudado -= $monto;
                        } elseif(intval($monto_recaudado) == 0){
                          $status = $none;
                          $monto_recaudado = 0;
                        } elseif($monto_recaudado > 0) {
                           $status = $abonado;
                        }

                        foreach ($_p as $id => $_pago) {
                          if($_pago != 0 && $status != $none && $monto != 0){
                            if($_pago > $monto){
                              array_push($ours,[$monto,$id]);
                              $_p[$id] -= $monto;
                              $monto -= $monto;
                            } elseif($_pago < $monto){
                              array_push($ours,[$_pago,$id]);
                              $_p[$id] -= $_pago;
                              $monto -= $_pago;
                            } else {
                              array_push($ours,[$_pago,$id]);
                              $_p[$id] -= $_pago;
                              $monto -= $_pago;
                            }
                          }
                        }

                        if($since != NULL && $every != NULL){
                          $_fechapago = null;
                          if($every <= 30){
                            $desde->addDays($every);
                          } elseif($every == 31){
                            $desde->addMonth(1);
                          }
                        }

                        if ($status == $abonado || ($status == $none && $desde->lt(\Carbon\carbon::now()))) {
                          $saldo_pendiente += $monto;
                        }

                      @endphp
                      @if ($status != "d-none")
                        <tr ar="hola" class="{{$status}}" {{$status == $none && $desde->lt(\Carbon\carbon::now()) ? "style=background-color:#FFEBEE!important;" : ""}}>
                          <td>
                            <small class="tidy">Número de pago</small><br/>
                            {{$o}}
                          </td>
                          <td>
                            <small class="tidy">Folio</small><br/>
                            {{"PP".$plan_sede->id."-".$o}}
                          </td>
                          <td>
                            <small class="tidy">Saldo</small><br/>
                            {{$amount->format($monto_)}}
                          </td>
                          @if ($status == $success)
                              <td>
                                <small class="tidy">Saldo pendiente</small><br/>
                                {{$amount->format($monto)}}
                              </td>
                              @php
                                $resta = 0;
                              @endphp
                            @else
                              <td>
                                <small class="tidy">Saldo pendiente</small><br/>
                                @php
                                  $resta = $monto-$monto_recaudado;
                                @endphp
                                {{$amount->format($saldo_pendiente)}}
                              </td>
                          @endif
                          <td>
                            <small class="tidy">Estado</small><br/>
                            {{($status == $success ? "Pagado" : ($monto_recaudado > 0 ? "Abono" : "Pendiente de pago"))}}
                          </td>
                          <td>
                            <small class="tidy">Abono</small><br/>
                            @php
                              $abono = $status == $success ? $monto_ : $monto-$resta;
                            @endphp
                            {{$amount->format($abono)}}

                          </td>
                          @php
                            $monto_recaudado = ($status==$abonado) ? 0 : $monto_recaudado;
                          @endphp
                          <td>
                            @php
                              if($since != NULL && $every != NULL){
                                echo '<small class="tidy">Fecha de pago</small><br/>';
                                echo $desde->format("Y-m-d");
                              }
                            @endphp
                          </td>
                        </tr>
                        @if (count($ours) > 0)
                        <tr ar="{{$status}}" class="bg-white">
                          <td colspan="7" style="padding:0;">

                                  @foreach ($ours as $paid)
                                    @php
                                      $pago = \App\pagos::find($paid[1]);
                                    @endphp
                                    <div class="row" style="margin:0;border:solid #f6f6f6 1px;">
                                      <div class="col">
                                        <small><b>Folio:</b>
                                          {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                                        </small>
                                      </div>
                                      <div class="col">
                                        <small><b>Monto:</b>
                                          {{$amount->format($pago->monto)}}
                                        </small>
                                      </div>
                                      <div class="col">
                                        <small><b>Fecha de pago:</b>
                                          {{\Carbon\Carbon::parse($pago->fecha_pago)->format("Y-m-d")}}
                                        </small>
                                      </div>
                                      <div class="col">
                                        <small><b>Comprobante:</b>
                                          {!!($pago->document_id == 0 ? "s/c" : "<a target='_blank' href='/ver/".md5($pago->document_id)."'>Ver comprobante</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>Descargar</a>")!!}
                                        </small>
                                      </div>
                                      <div class="col">
                                        <small><b>Método de pago:</b>
                                          {{$pago->clave}}
                                        </small>
                                      </div>
                                      <div class="col">
                                        <small><b>Monto recaudado:</b>
                                          {{$amount->format($paid[0])}}
                                        </small>
                                      </div>
                                    </div>
                                  @endforeach
                          </td>
                        </tr>
                        @endif
                      @endif
                      @if ($plan_sede->disable == null)
                        <script type="text/javascript">
                          document.getElementsByClassName("saldo_{{$plan_sede->id}}")[0].innerText = "{{$amount->format($saldo_pendiente)}}";
                        </script>
                      @endif
                    @endfor
                  </tbody>
                </table>
            </div>
            @endif
          </div>
        @endforeach
      @else
        <div class="card">
          <div class="card-body">
            <h3 class="text-center">
              <i class="fas fa-exclamation-triangle text-warning"></i> No hay planes de pago
            </h3>
          </div>
        </div>
    @endif
  </div>
</div>
@endsection
@section('styles')
  <link rel="stylesheet" href="/css/table.css">
@endsection
@section('scripts')
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
  <script type="text/javascript">
    lang.order = [0,"asc"];

    $(() => {
        var editable = false;
        var editabledays = false;
        var editablebecas = false;
        $(".editable").bind("dblclick",function(){
          if(!editable){
            editable = true;
            $(this).find(".fecha").addClass("d-none");
            let input = $("<input>").attr("type","date").addClass("form-control date");
            input.val(new Date($(this).find(".fecha").text()).toISOString().split('T')[0]);
            input.bind("change", () =>{
              let nVal = $(this).find("input").val();
            });

            input.bind("keypress",(e) => {
              if(e.keyCode == 13){
                ShowWaitNotify("Cambiando información de pago");
                let cid = ($(this).parent().find(".editable").attr("cid"));
                let fecha = $(this).parent().find("input").val();
                let spamfecha = $(this).parent().find(".fecha");
                $(this).parent().find(".fecha").removeClass("d-none");
                input.remove();
                $.post("/planes/setfecha?_token={{csrf_token()}}&cid="+cid+"&since="+fecha,function(data){
                  ShowSuccessNotify("Información modificada");
                  spamfecha.html(fecha).css({"color":"green"});
                  editable = false;
                  location.reload();
                });
              }
            });

            $(this).append(input);
          }
        });

        $(".editableday").bind("dblclick",function(){
          if(!editabledays){
            editabledays = true;
            $(this).find(".day").addClass("d-none");

            let input = $("<input>").attr("max","31")
            .attr("min","1")
            .attr("step","1")
            .attr("type","number")
            .addClass("form-control dayinput");

            let day = $(this).find(".day").attr("dias");
            input.val(day);
            input.bind("change", () =>{
              let nVal = $(this).find("input").val();
            });

            input.bind("keypress",(e) => {
              if(e.keyCode == 13){
                ShowWaitNotify("Cambiando");
                let cid = ($(this).parent().find(".editableday").attr("cid"));
                let dia = $(this).parent().find("input").val();
                let spamfecha = $(this).parent().find(".day");
                $(this).parent().find(".day").removeClass("d-none");
                input.remove();
                $.post("/planes/setdias?_token={{csrf_token()}}&cid="+cid+"&every="+dia,function(data){
                  location.reload();
                  ShowSuccessNotify("Información modificada");
                  spamfecha.html(fecha).css({"color":"green"});
                  editabledays = false;
                });
              }
            });

            $(this).append(input);
          }
        });

        $(".editablebeca").bind("dblclick",function(){
          if(!editablebecas){
            editablebecas = true;
            $(this).find(".beca").addClass("d-none");

            let input = $("<input>")
            .attr("min","0")
            .attr("step",".1")
            .attr("type","number")
            .addClass("form-control becainput");

            let beca = $(this).find(".beca").attr("beca");
            input.val(beca);
            input.bind("change", () =>{
              let nVal = $(this).find("input").val();
            });

            input.bind("keypress",(e) => {
              if(e.keyCode == 13){
                ShowWaitNotify("Cambiando");
                let cid = ($(this).parent().find(".editablebeca").attr("cid"));
                let bec = $(this).parent().find("input").val();
                let spamfecha = $(this).parent().find(".beca");
                $(this).parent().find(".beca").removeClass("d-none");
                input.remove();
                $.post("/planes/setbeca?_token={{csrf_token()}}&cid="+cid+"&beca="+bec,function(data){
                  location.reload();
                  ShowSuccessNotify("Información modificada");
                  spamfecha.html(fecha).css({"color":"green"});
                  editablebecas = false;
                });
              }
            });

            $(this).append(input);
          }
        });

        $(".question").bind("click", () => {
          event.preventDefault();
          Swal.fire("Ayuda","Sí eliges entre 1 y 30, transcurriran esas cantidad de días entre cada pago, sí eliges 31, el pago será requerido el mismo día de la fecha de inicio.","question");
        });
        $(".bq").bind("click", () => {
          event.preventDefault();
          Swal.fire("Ayuda","Coloca el monto de la colegiatura sin beca, la beca se calculará en función; Sí colocas 0, beca no saldrá en el estado de cuenta.","question");
        });

       $(".actualizar").bind("click",function(){
          event.preventDefault();
          ShowWaitNotify("Guardando");
          let cid = $(this).attr("cid");
          let since = $(".since-"+cid).val();
          let every = $(".every-"+cid).val();

          if (since != "" && every != "") {
            $.post("/planes/actualizar",{
              "since":since,
              "every":every,
              "_token":"{{csrf_token()}}",
              "cid":cid
            },function(data){
              if(data){
                ShowSuccessNotify("Hecho!");
                location.href = "#tb_Logbook"+cid;
                location.reload();
              } else {
                ShowErrorNotify("Error en el transporte de datos");
              }
            });
          } else {
            ShowErrorNotify("Debes de agregar una fecha y un día");
          }

       });

       $(".print").bind("click",function(event){
        event.preventDefault();
        ShowWaitNotifyTime("Generando");
        var pdf = new jsPDF('', 'pt', 'a4');

        pdf.internal.scaleFactor = 2;
        var el = $(this).parent().parent().parent().parent().parent();
        var botones = $(this).parent();
        botones.addClass("d-none");
        $(".detener").addClass("d-none");
        $(".titulo").addClass("d-none");
        $(".desc").removeClass("d-none");
        $(".logoa").removeClass("d-none");
        $("tr[class='']").addClass("d-none");
        $("tr[style]").removeClass("d-none");
        if($(".beca").attr("beca") == 0){
          $(".panelbeca").addClass("d-none");
        }
        var options = {
          pagesplit:true,
        };
        el.css("background", "#fff");
        var tempWidth = el.css("width");
        var printHtml = el.get(0);
        html2pdf()
    		.set({ html2canvas: { scale: 4 } })
    		.from(printHtml)
    		.save("{{$fecha ?? ''." - ".$nombre}}").then(
          function(){
            // pdf.addHTML(printHtml,15, 15, options,function() {
            //     //pdf.save('{{$fecha ?? ''." - ".$nombre}}.pdf');
                ShowSuccessNotify("Listo!");
                //el.css("width",tempWidth);
                botones.removeClass("d-none");
                $(".detener").removeClass("d-none");
                $(".titulo").removeClass("d-none");
                $(".desc").addClass("d-none");
                $(".logoa").addClass("d-none");
                $("tr[class='d-none']").removeClass("d-none");
                $(".panelbeca").removeClass("d-none");
            // });
          }
        );
       });
    })

    $(".nplan").bind("click",function(){
        $(".nuevoplan").toggle();
    });
    $(".dela").bind("click",function(){
      Swal.fire(
        '¡Ups!',
        'Primero borra los pagos aplicados',
        'question'
      );
    });
    $(".del").bind("click",function(){
      Swal.fire({
        icon: 'warning',
        title: '¿Deseas eliminar el plan seleccionado?',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
      }).then((result) => {
        if (result.isConfirmed) {
          $(this).find("i").removeClass("fa fa-trash");
          $(this).find("i").addClass("fas fa-cog fa-spin");
          let e = $(this);
          $.post("/alumnos/delplanpagos?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
            console.log(e.parent().parent().parent().parent().parent().remove());
          });
        }
      });
    });
    $(".detener").bind("click",function(){
      Swal.fire({
        icon: 'warning',
        title: '¿Deseas detener el cobro del plan?',
        text: "Detener el plan actual conservará los pagos ya aplicados incluyendo abonos pero evitará que se puedan agregar más pagos en el futuro",
        showCancelButton: true,
        confirmButtonText: 'Detener',
      }).then((result) => {
        if (result.isConfirmed) {
          $(this).find("i").removeClass("fa fa-trash");
          $(this).find("i").addClass("fas fa-cog fa-spin");
          let e = $(this);
          $.post("/alumnos/detplanpagos?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
            location.reload();
          });
        }
      });
    });
  </script>
@endsection
