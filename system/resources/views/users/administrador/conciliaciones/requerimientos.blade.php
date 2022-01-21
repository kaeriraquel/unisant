@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="row">
    <div class="col-12">
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
        $perpay = $c3 == 0 ? 0 : $total_r / $c3;
      @endphp
      <div class="card">
          <div class="card-body">
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
            <b>Sede:</b> {{$c->sede->sede}} {!!!$c->estado ? "(En espera)" : '(Conciliado el '.$c->updated_at.')'!!}</br>
            <b>Creado el:</b> {{\Carbon\Carbon::parse($c->created_at)->format("Y-M-d")}}<br>
            <small><b>Desde:</b> {{$c->desde}} <b>al:</b> {{$c->hasta}}</small>
            <hr>
            <div class="row">
              <div class="col-4 text-center">
                <h3 class="text-dark">
                  {{$amount->format($total_p)}}
                </h3>
                <h6>RECAUDACIÓN TOTAL</h6>
              </div>
              <div class="col-8">
                <br>
                <div class="row datos">
                  <div class="col text-center">
                    <h4 class="text-dark">
                      {{count($c->pagos)}}
                    </h4>
                    <h6>Pagos</h6>
                  </div>
                  <div class="col text-center">
                    <h4 class="text-dark">
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
                  <table class="conceptos table table-striped" data-page-length="200">
                    <thead>
                      <th>Distribución</th>
                      <th>Monto</th>
                      <th>Permitir en sede</th>
                    </thead>
                    <tbody>
                      @php
                        $suma_total = 0;
                      @endphp
                      @foreach ($c->conceptos as $con)
                        @php
                          $valsum = str_replace("$","",str_replace(",","",$con->value));
                          if (strstr($con->keyval,"UTILIDAD_-_")) {
                            $suma_total += $valsum;
                          }
                          $some = $valsum <= 0 ? "danger" : "dark";
                        @endphp
                        <tr>
                          <td>{{$con->keyval}}</td>
                          <td class="text-{{$some}}">{{$con->value}}</td>
                          <td>
                            <a href="/conciliaciones/comm_switchconcepto?cid={{md5($con->id)}}">
                              {{$con->show_sede == NULL ? "Mostrar" : "Ocultar"}}
                            </a>
                          </td>
                        </tr>
                      @endforeach
                      @php
                        $some = $suma_total <= 0 ? "danger" : "success";
                      @endphp
                      <tr>
                        <td>
                          <b>Rentabilidad</b>
                        </td>
                        <td class="text-{{$some}}">
                          <b>
                            {{$amount->format($suma_total)}}
                          </b>
                        </td>
                        <td>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
          </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          @php
            $ids = [];
            $_desde = \Carbon\carbon::parse($c->desde);
            $_hasta = \Carbon\carbon::parse($c->hasta);
            foreach ($c->opciones as $ey)
              $ids[$ey->concepto_id] = $ey->concepto_id;
          @endphp
          <div class="clearfix">
            <div class="float-left">
              <h3>Pagos adjuntos</h3>
            </div>
            <div class="float-right">
              @if ($c->estado == NULL)
                <form action="/dist/debug" method="post">
                  @csrf
                  <button type="submit" style="cursor:pointer;background:none;border:0;margin:0;padding:0;">
                    @if (session()->has("debug"))
                        <i class="fas fa-toggle-on text-success fa-2x"></i>
                      @else
                        <i class="fas fa-toggle-off text-danger fa-2x"></i>
                    @endif
                  </button>
                </form>
              @endif
            </div>
          </div>
          <hr>
          <table id="materias" class="table table-striped display nowrap" style="width:100%" {!!$c->estado?:"data-page-length='200'"!!}>
            <thead>
              @if (session()->has("debug"))
                <th>Valores</th>
              @endif
              <th>Folio</th>
              <th></th>
              <th>Monto</th>
              <th>Pagos</th>
              <th>Cargos</th>
              <th>Matrícula</th>
              <th>Alumno</th>
              <th>Grupo</th>
              <th>Fecha de pago</th>
              <th>Fecha cubierta</th>
              <th>Terminológia de pago</th>
              <th>Comprobante</th>
              <th>Tipo de pago</th>
              <th>Concepto</th>
            </thead>
            <tbody>
              @php
                $concepto = [];
                $conceptos = [];
                $opcionales = [];
                $nodist = [];
                $clock = 0;
                $planes_ids = [];
                $pagos_ids = [];
              @endphp
              @foreach ($c->pagos as $pago)
                @if ($c->estado == NULL && $pago->returned_at == NULL)
                @php
                  if($pago->plan_pagos){
                    array_push($planes_ids,$pago->plan_pagos->alumno_plan_id);
                    array_push($pagos_ids,$pago->id);
                  }
                  if(Request::has("trans")){
                    $pago->clave = "Tarjeta";
                    $pago->save();
                  }
                  $grupo = $pago->grupo;
                  $conceptos = ($grupo->dist_grupos != null) ?  $grupo->dist_grupos->dist->conceptos : [];
                  if($grupo->dist_grupos == null){
                    $dc = \App\dist_grupos::whereRAW("replace(grupo,' ','')='".str_replace(" ","",$grupo->grupo)."'")->first();
                    if($dc != null){
                      $conceptos = $dc->dist->conceptos;
                    }
                  }
                  if ($grupo->dist_grupos == null && count($conceptos) == 0) {
                    if(isset($nodist[$grupo->grupo])){
                      $nodist[$grupo->grupo]++;
                    } else {
                      $nodist[$grupo->grupo] = 1;
                    }
                  }

                  $monto_pago = ($total_r > 0) ? $pago->monto - $perpay : $pago->monto;

                  if($pago->extra != NULL){
                    if($pago->metodo == "%"){
                      $por = $monto_pago * ($pago->extra / 100);
                      $monto_pago += $por;
                    } elseif($pago->metodo == "$") {
                      $monto_pago += $pago->extra;
                    }

                  }
                  $montos[$pago->id] = $monto_pago;
                  $t_monto = $monto_pago;


                  if(count($conceptos) > 0)
                  {
                    foreach ($conceptos as $ct){
                      if ($ct->opcional == 1) {
                        $opcionales[$ct->id] = $ct;
                      }
                    }
                    $aplicados = 0;
                    $pasarela = false;
                    $utilidad = [];
                    $jales = $pago->cantidad_pagos == NULL ? 1 : $pago->cantidad_pagos;
                    for($h = 0; $h < $jales; $h++){
                      foreach ($conceptos as $cto) {
                        if (!in_array($cto->id,$ids)) {
                          if(!isset($concepto[$cto->concepto]))
                            $concepto[$cto->concepto] = 0;
                          if (($jales == $h+1) && !in_array($cto->id,$utilidad) && (strstr($pago->concepto,"Colegiaturas") && $cto->tipo == "Porcentaje sobre utilidad")) {
                            if (!isset($concepto["UTILIDAD - ".$cto->concepto])) {
                              $concepto["UTILIDAD - ".$cto->concepto] = 0;
                            }
                            $re = $monto_pago * $cto->cantidad/100;
                            $concepto["UTILIDAD - ".$cto->concepto] += $re;
                            $aplicados++;
                            $utilidad[$cto->id] = $cto->id;
                          } elseif ($cto->tipo == "Monto fijo por") {
                            if(strstr($pago->concepto,$cto->concepto_pago->concepto)){
                              if(strstr($pago->cantidad,"%")){
                                $cantidad = str_replace("%","",$pago->cantidad)/100;
                                $dis = $monto_pago * $cantidad;
                                $monto_pago -= $dis;
                                $concepto[$cto->concepto] = $dis;
                              } else {
                                $monto_pago -= $cto->cantidad;
                                $concepto[$cto->concepto] += $cto->cantidad;
                              }
                              $aplicados++;
                            }
                          } elseif (strstr($pago->concepto,"Colegiaturas") && $cto->tipo == "Monto sí hay pasarela") {
                            if(($pago->pasarela != NULL || $pago->clave == "Tarjeta") && $pasarela==false){
                              //$monto_pago -= $cto->cantidad;
                              $comision = $pago->pasarela != NULL ? $pago->pasarela->comision/100 : 0.029;
                              $fijo = $pago->pasarela != NULL ? $pago->pasarela->fijo : 2.5;
                              $siiva = $pago->pasarela != NULL ? ($pago->pasarela->iva == 1 ? 1 : 0) : 1;
                              $extra = $pago->pasarela != NULL ? " ".$pago->pasarela->name : " Default";
                              $pasarela = ($comision * $montos[$pago->id]) + $fijo;
                              $iva = $pasarela * \App\keys::where("key","iva")->first()->value/100 * $siiva;
                              $tpasa = $pasarela + $iva;
                              $monto_pago -= $tpasa;
                              if(!isset($concepto[$cto->concepto.$extra]))
                                $concepto[$cto->concepto.$extra] = 0;
                              $concepto[$cto->concepto.$extra] += $tpasa;
                              $aplicados++;
                              $pasarela = true;
                            }
                          } elseif(strstr($pago->concepto,"Colegiaturas") && $cto->tipo == "Monto sobre utilidad") {
                            $monto_pago -= $cto->cantidad;
                            $concepto[$cto->concepto] += $cto->cantidad;
                            $aplicados++;
                          }
                        }
                      }
                    }
                    if ($aplicados == 0) {
                      if(!isset($concepto["Sin distribución asignada"]))
                        $concepto["Sin distribución asignada"] = 0;
                      $concepto["Sin distribución asignada"] += $pago->monto;
                    }
                  } else {
                    if(!isset($concepto["Sin distribución asignada"]))
                      $concepto["Sin distribución asignada"] = 0;
                    $concepto["Sin distribución asignada"] += $pago->monto;
                  }
                @endphp
                @php
                  if(count($c->terms) != count($c->pagos)){
                    $planes_sede = \App\alumnos_planes::findMany(array_unique($planes_ids));
                    $_e = [];
                    $_p = [];
                    foreach ($planes_sede as $plan) {
                        $monto_recaudado = 0;
                        $since = $plan->since;
                        $every = $plan->every;
                        $desde = \Carbon\carbon::parse($since);

                        if($since != NULL && $every != NULL){
                          if($every <= 30){
                            $desde->subDays($every);
                          } elseif($every == 31){
                            $desde->subMonth(1);
                          }
                        }

                        foreach ($plan->planes_pagos as $pp) {
                          if($pp->pago != null){
                            $monto_recaudado += $pp->pago->monto;
                            $_p[$pp->pago->id] = $pp->pago->monto;
                          }
                        }


                        for ($o = 1; $o <= $plan->plan->plazo;$o++){
                          $success = "bg-success";
                          $abonado = "bg-warning";
                          $none = "d-none";
                          $ours = [];
                          $ours_id = [];
                          $monto_ = $plan->plan->monto/$plan->plan->plazo;
                          $monto = $plan->plan->monto/$plan->plan->plazo;
                          $status = $none;

                          if($monto <= $monto_recaudado){
                            $status = $success;
                            $monto_recaudado -= $monto;
                          } elseif(intval($monto_recaudado) == 0){
                            $status = $none;
                            $monto_recaudado = 0;
                          } elseif($monto_recaudado > 0) {
                             $status = $abonado;
                             $monto_recaudado = 0;
                          }

                          foreach ($_p as $id => $_pago) {
                            if($_pago != 0 && $status != $none && $monto != 0){
                              if($_pago > $monto){
                                array_push($ours,$id);
                                $_p[$id] -= $monto;
                                $monto -= $monto;
                              } elseif($_pago < $monto){
                                array_push($ours,$id);
                                $_p[$id] -= $_pago;
                                $monto -= $_pago;
                              } else {
                                array_push($ours,$id);
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

                          $cubierta = $desde;
                          $first = $cubierta->copy();
                          $second = $cubierta->copy();
                          $fpago = \Carbon\Carbon::parse($pago->fecha_pago);

                          if($every <= 30){
                            $first->subDays($every)->addDays(1);
                            $second->addDays($every)->subDays(1);
                          } elseif($every == 31){
                            $first->subMonth(1)->addDays(1);
                            $second->addMonth(1)->subDays(1);
                          }
                          foreach($ours as $or){
                            if(in_array($or,$pagos_ids)){
                              $orden = ($fpago->between($first,$second)) ? "normal" : (
                                $cubierta->lt($fpago) ? "retrasado" : (
                                  $cubierta->gt($fpago) ? "adelantado" : "no reconocible"
                                  )
                              );
                              if(isset($_e[$or])){
                                $_e[$or][0] .= ", ".$desde->format("Y-m-d");
                                $_e[$or][1] .= ", ".$orden;
                              } else {
                                $_e[$or] = [$desde->format("Y-m-d"),$orden];
                              }
                              //echo "$first $cubierta $second </br>";

                            }
                          }
                        }
                      }
                  }
                @endphp
              @endif
              <tr>
                @if (session()->has("debug") && $c->estado == NULL)
                  <td>
                    <table>
                        @php
                          if ($total_r > 0) {
                            echo "<td class='text-color:red;'>$perpay</td>";
                          }
                          $pasarela = false;
                          $utilidad = [];

                          $monto_pago = $montos[$pago->id];

                          for($h = 0; $h < $jales;$h++){
                            echo "<tr>";
                            foreach ($conceptos as $cto) {
                            if (!in_array($cto->id,$ids)) {
                              if ((strstr($pago->concepto,"Colegiaturas") && $cto->tipo == "Porcentaje sobre utilidad") && !in_array($cto->id,$utilidad)) {
                                echo "<td>".($monto_pago * $cto->cantidad/100)."</td>";
                                $utilidad[$cto->id] = $cto->id;
                              } elseif ($cto->tipo == "Monto fijo por") {
                                if(strstr($pago->concepto,$cto->concepto_pago->concepto)){
                                  if(strstr($pago->cantidad,"%")){
                                    $cantidad = str_replace("%","",$pago->cantidad)/100;
                                    $dis = $monto_pago * $cantidad;
                                    echo "<td>".$dis."</td>";
                                  } else {
                                    echo "<td>".$cto->cantidad."</td>";
                                  }
                                }
                              } elseif (strstr($pago->concepto,"Colegiaturas") && $cto->tipo == "Monto sí hay pasarela") {
                                  if(($pago->pasarela != NULL || $pago->clave == "Tarjeta") && $pasarela==false){
                                  $comision = $pago->pasarela != NULL ? $pago->pasarela->comision/100 : 0.029;
                                  $fijo = $pago->pasarela != NULL ? $pago->pasarela->fijo : 2.5;
                                  $siiva = $pago->pasarela != NULL ? ($pago->pasarela->iva == 1 ? 1 : 0) : 1;
                                  $extra = $pago->pasarela != NULL ? " ".$pago->pasarela->name : " Default";
                                  $pasarela = ($comision * $monto_pago) + $fijo;
                                  $iva = $pasarela * \App\keys::where("key","iva")->first()->value/100 * $siiva;
                                  $tpasa = $pasarela + $iva;
                                  $monto_pago -= $tpasa;
                                  $pasarela = true;

                                  echo "<td class='fireform' formula='($comision * $monto_pago+ $fijo)+ ($iva)'><a href='#'>".$tpasa."</a></td>";
                                }
                              } elseif(strstr($pago->concepto,"Colegiaturas") && $cto->tipo == "Monto sobre utilidad") {
                                echo "<td>".$cto->cantidad."</td>";
                              }
                            }
                          }
                          echo "</tr>";
                          }
                        @endphp
                    </table>
                  </td>
                @endif
                <td>
                  {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                </td>
                <td>
                  @if ($pago->returned_at != NULL)
                    <i class="fas fa-undo-alt text-danger"></i>
                    @if ($pago->estado == 10)
                      <i class="far fa-clock"></i>
                      @php
                        $clock ++;
                      @endphp
                    @endif
                  @elseif ($pago->deleted_at != NULL)
                    <i class="fas fa-eraser"></i>
                  @else
                    <i class="fas fa-check-circle"></i>
                  @endif
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
                        {{$pago->metodo.$pago->extra}} ({{$amount->format($t_monto)}})
                      </div>
                  @endif
                </td>
                <td>
                  {{$pago->matricula}}
                </td>
                <td>
                  @php
                    $nombre = \App\nombres::where("matricula",$pago->matricula)->first();
                  @endphp
                    @if (auth()->user()->nivel->name == "AdministradorEscolar")
                      <a class="text-danger"href="#">
                      @else
                        <a class="text-danger" href="/alumnos/pagos?cid={{base64_encode($pago->matricula)}}&did={{$c->sede->id}}&pago={{md5($pago->id)}}">
                    @endif
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
                  {{\Carbon\carbon::parse(($pago->fecha_pago ?: NULL))->format("Y-m-d")}}
                </td>
                @php
                  $term = \App\terminologia_fecha::where("desde",$_desde)
                  ->where("hasta",$_hasta)
                  ->where("pago_id",$pago->id)->first();
                  if($term == NULL){
                    $term = \App\terminologia_fecha::create([
                      "hasta" => $_hasta->format("Y-m-d"),
                      "desde" => $_desde->format("Y-m-d"),
                      "fecha" => $_e[$pago->id][0],
                      "status" => $_e[$pago->id][1],
                      "pago_id" => $pago->id,
                      "conciliacion_id" => $c->id
                    ]);
                  }
                @endphp
                <td>
                  {{$term != NULL ? $term->fecha : $_e[$pago->id][0]}}
                </td>
                <td>
                  {{mb_strtoupper($term != NULL ? $term->status : $_e[$pago->id][1])}}
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
        @foreach ($nodist as $key => $value)
          <div class="alert alert-warning text-dark">
            {{$key}} Sin distribución, {{$value}} incidencias;
          </div>
        @endforeach
        @if (count($opcionales) > 0 && $c->estado == NULL)
          <hr>
          <h3>Conceptos Opcionales a Descontar</h3>
          <p>Marque los conceptos que <b>NO DESEA</b> considerar para la conciliación, después presione el botón marcado como recalcular.</p>
          <form action="/dist/addopciones" method="post">
            @csrf
            <input type="hidden" name="conciliacion_id" value="{{$c->id}}">
            @foreach ($opcionales as $op)
              @php
                if (in_array($op->id,$ids)) {
                  $ceros[$op->id] = $op->id;
                }
              @endphp
              <input type="checkbox" {{in_array($op->id,$ids) ? "checked" : ""}} id="op_{{$op->id}}" name="opciones[]" value="{{$op->id}}">
              <label class="text-dark" for="op_{{$op->id}}">{{$op->concepto}}</label>
            @endforeach
            <br>
            <br>
            <hr>
            <input type="submit" class="btn btn-primary" value="Recalcular">
          </form>
        @endif
        </div>
      </div>
    </div>
  </div>
  @if (count($c->requerimientos) > 0)
    <div class="row">
      <div class="col">
        <div class="card">
          <div class="card-body">
            <h3>Requerimientos incluidos</h3>
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
          </div>
        </div>
      </div>
    </div>
  @endif
  @if ($c->estado == NULL)
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h3>Resultados de distribución:</h3>
          <hr>
          <table class="table table-striped resultados">
            <thead>
              <th>
                Concepto
              </th>
              <th>
                Monto
              </th>
            </thead>
            <tbody>
              @foreach ($c->opciones as $op)
                @if (isset($ceros[$op->concepto_id]))
                  <tr>
                    <td>{{$op->concepto->concepto}}</td>
                    <td>{{$amount->format(0)}}</td>
                  </tr>
                @endif
              @endforeach
              @foreach ($concepto as $key => $value)
                <tr>
                  <td>{{$key}}</td>
                  <td>{{$amount->format($value)}}</td>
                </tr>
              @endforeach
              @if ($total_r >0)
                <tr>
                  <td>Requerimientos</td>
                  <td>{{$amount->format($total_r)}}</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
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
        </div>
      </div>
    </div>
  </div>
@endif
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h3>Acciones de administrador</h3>
          <hr>
          @if ($c->estado == NULL)
            <div class="clearfix">
              <div class="float-left">
                <form action="/conciliaciones/conciliar" method="post">
                  @csrf
                  <input type="hidden" name="cid" value="{{Request::get("cid")}}">
                  @if ($clock > 0)
                      <input class="btn btn-disabled" type="button" value="Revisa los pagos pendientes de devolución">
                    @else
                      <input class="btn btn-primary" type="submit" value="Conciliar">
                  @endif
                  <table>
                    @foreach ($c->opciones as $op)
                      @if (isset($ceros[$op->concepto_id]))
                        <tr>
                          <td>
                            <input type="hidden" name="{{$op->concepto->concepto}}" value="{{$amount->format(0)}}">
                          </td>
                        </tr>
                      @endif
                    @endforeach
                    @foreach ($concepto as $key => $value)
                      <tr>
                        <td>
                          <input type="hidden" name="{{$key}}" value="{{$amount->format($value)}}">
                        </td>
                      </tr>
                    @endforeach
                    @if ($total_r >0)
                      <tr>
                        <td>
                          <input type="hidden" name="Requerimientos" value="{{$amount->format($total_r)}}">
                        </td>
                      </tr>
                    @endif
                  </table>
                </form>
              </div>
              <div class="float-right">
                @if ($c->estado == null)
                  <form class="formdeshacer" action="/conciliaciones/deshacerad" method="post">
                    @csrf
                    <input type="hidden" name="cid" value="{{md5($c->id)}}">
                  </form>
                @endif
                <button class="btn btn-danger deshacer" type="button">Deshacer</button>
              </div>
            </div>
            @else
              <a href="#" cid={{md5($c->id)}} class="desc btn btn-primary">Desconciliar</a>
          @endif
        </div>
      </div>
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
        $(".resultados").DataTable({
           "order": [[ 1, "desc" ]],
            dom: 'Bfrtip',
            "language":lang.language,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        @if ($c->estado==NULL)
        @else
        $(".conceptos").DataTable({
           "order": [[ 1, "desc" ]],
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
        @endif
      });
      $(function(){
        $(".table2").DataTable(lang);
        $(".fireform").bind("click",function(){
          Swal.fire("Formula",$(this).attr("formula"));
        });

        $("#materias").DataTable({
            "scrollX":true,
            dom: 'Bfrtip',
            "language":lang.language,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
      });
      @if($c->estado != NULL)
        $(".desc").bind("click",function(){
          Swal.fire({
            icon: 'warning',
            title: '¿Deseas desconciliar?',
            text: "Desconciliar efecturá la eliminación de todos los valores actuales y los recalculará apartir de los valores de distribución asignados actualmente.",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              $(this).find("i").removeClass("fa fa-trash");
              $(this).find("i").addClass("fas fa-cog fa-spin");
              let e = $(this);
              $.post("/conciliaciones/desconciliar?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
                location.reload();
              });
            }
          });
        });
      @else
        $(".deshacer").bind("click",function(){
          Swal.fire({
            icon: 'warning',
            title: '¿Deseas deshacer la conciliación?',
            text: "Esta acción devolverá los pagos a la sede de procedencia",
            showCancelButton: true,
            confirmButtonText: 'Continuar',
          }).then((result) => {
            if (result.isConfirmed) {
              $(".formdeshacer").submit();
            }
          });
        });
      @endif
      @if (isset($suma_total))
          $(function(){
            let vd = $("<div>").addClass("col text-center text-{{$some}}");
            vd.append($("<h4>").text("{{$amount->format($suma_total)}}"));
            vd.append($("<h6>").text("Rentabilidad"));
            $(".datos").append(vd);
          });
      @endif
  </script>
@endsection
