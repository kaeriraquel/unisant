@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">
        @php

        $sede = (isset(Auth::user()->sede)) ? auth()->user()->sede->sede->sede: "Administrador";
        $individual = Request::has("did") ? NULL : \App\sedes::where("sede",$sede)->first();;
        if($individual == null){
          $individual = \App\sedes::find(Request::get("did"));
        }

        $sede_id = $individual->id;
        $url = $individual->individual."&matricula=".base64_decode(Request::get("cid"));
        $alumno = \App\alumnosest::where("matricula",base64_decode(Request::get("cid")))->first();
        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($res,true);
        if(isset($data["response"])){
          $x = 0;
          $al = (object) $data["response"];
          $t = count($al->materias_cursadas);
          $rep = 0;
          foreach($al->materias_cursadas as $m){
            $m = (object) $m;
            if(floatval($m->calificacion) < 7)
              $rep++;
          }
          $apr = count($al->materias_cursadas) - $rep;
          $al->sede = (object) ["sede"=>$sede];
          $al->sede_id = $sede_id;
          $al->planes = \App\alumnos_planes::where("matricula",$al->clave_alumno)->get();
          $grupo = \App\grupos::where("matricula",$al->clave_alumno)->first();
          if($grupo == null){
            $grupo = \App\grupos::create(["alumnoest_id"=>0,"grupo"=>"Sin grupo","matricula"=>$al->clave_alumno]);
          }
          $al->grupo = $grupo;
        } elseif($alumno == NULL){
          dd("Revisa el API de Conexión");
          exit();
        } else {
          $al = $alumno ?: ((object) []);
          $al->clave_alumno = $alumno->matricula;
          $al->nombre = $alumno->nombre_completo;
          $al->primer_apellido = $alumno->apat;
          $al->segundo_apellido = $alumno->amat;
          $al->materias_cursadas  = [];
          $al->carrera = $alumno->revoe->clave." - ".$alumno->revoe->nombre;
          $al->estado_alumno = "Activo";
          $al->sede = $alumno->sede;
          $al->sede_id = $sede_id;
          $al->baja = $alumno->baja;
          $al->planes = $alumno->planes;
          /// Cal Cal
          $apr = 0;
          $rep = 0;
          $t = 0;
        }

        if(isset($alumno)){
          $grupo = $alumno->grupo;
          if($grupo != null){
            $al->grupo = $grupo->grupo;
          } else {
            $al->grupo = "Sin grupo";
          }
        } else {
          $al->grupo = $al->grupo->grupo;
          $al->baja = $al->estado_alumno;
        }

        $menu = [
          "Nuevo pago" => "/alumnos/nuevopago?cid=".urlencode(Request::get('cid').(Request::has("did")?"&did=".Request::get("did"):"")),
          "Planes de pago" => "/alumnos/planes?cid=".urlencode(Request::get('cid')),
        ];
        @endphp
      </div>
    </div>
    <form class="alumnog" action="/sedes/actualizarmonto" method="post">
      @if (Request::has("did"))
        <input type="hidden" name="did" value="{{Request::get("did")}}">
      @endif
    <div class="row">
      <div class="col-md-12">
        <div class="clearfix">
          <div class="float-left">
            <h3>Información de alumno</h3>
          </div>
          <div class="float-right">
            <a href="/alumnos/lista{{isset($x) ? "" : "est"}}">
              Lista de alumnos
            </a>
          </div>
        </div>
        <hr>
        {{session("status")}}
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-12">
            Matricula:
          </div>
          <div class="col-md-12">
            <input type="text" name="clave_alumno" class="allow form-control" value="{{$al->clave_alumno}}">
          </div>
          <div class="col-md-12">
            Nombre:
          </div>
          <div class="col-md-4">
            <input type="text" placeholder="Nombre(s)" name="nombre" class="allow form-control" value="{{$al->nombre}}">
          </div>
          <div class="col-md-4">
            <input type="text" placeholder="Apellido paterno" name="apat" class="allow form-control" value="{{$al->primer_apellido}}">
          </div>
          <div class="col-md-4">
            <input type="text" placeholder="Apellido materno" name="amat" class="allow form-control" value="{{$al->segundo_apellido}}">
          </div>
          <div class="col-md-12">
            Sede:
          </div>
          <div class="col-md-12">
            @if (Auth::user()->nivel->nivel == "Administrador" || \Session::has("admin"))
                <select class="form-control allow" name="sede_id">
                  @foreach (\App\sedes::all() as $_sede)
                    <option {{$_sede->id == $al->sede_id ? "selected" : ""}} value="{{$_sede->id}}">{{$_sede->sede}}</option>
                  @endforeach
                </select>
              @else
                <input type="text" disabled class="disabled form-control" value="{{$al->sede->sede}}">
            @endif
          </div>
          <div class="col-md-12">
            Carrera:
          </div>
          <div class="col-md-12">
            <input type="text" disabled class="disabled form-control" value="{{$al->carrera}}">
          </div>
          <div class="col-md-12">
            Estado del alumno:
          </div>
          <div class="col-md-12">
            @php
              $estado = $al->estado_alumno == NULL ? \App\estadosdelalumno::where("estado","1")->first() :
               (\App\estadosdelalumno::where("id",$al->estado_alumno)->first() != NULL ?
                \App\estadosdelalumno::where("id",$al->estado_alumno)->first() :
                 ((object)["name"=>$al->estado_alumno,"background"=>"#0099bb","color"=>"#FFF"]));
            @endphp
            <div class="row">
                @if (isset($x))
                  <div class="col">
                    <div class="form-control">
                      {{$estado->name}}
                    </div>
                  </div>
                  @else
                    <div class="col">
                    <select class="form-control allow" name="estado_alumno">
                      @foreach (\App\estadosdelalumno::all() as $est)
                        <option {{$est->name == $estado->name ? "selected" : ""}} value="{{$est->id}}">{{$est->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  @if ($estado->name != NULL)
                    <div class="col-2 text-center">
                      <table align="center" height="100%">
                        <tr>
                          <td>
                            <div class="badge" style="background-color:{{$estado->background}};color:{{$estado->color}};">
                              {{$estado->name}}
                            </div>
                          </td>
                        </tr>
                      </table>
                    </div>
                  @endif
                @endif

            </div>
          </div>
          <div class="col-md-12">
            Pago del alumno ({{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}}):
          </div>
          <div class="col-md-12">
            @php
            $cantidad = 0;
            $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
            $total_pagado = 0;
            $monto = \App\montos::where("matricula",$al->clave_alumno)->first();
            if($monto==null)
              $monto = \App\montos::create(["matricula"=>$al->clave_alumno,"porcentaje_materia"=>(Auth::user()->sede ? Auth::user()->sede->sede->monto : $al->sede->monto)]);
            @endphp
              @csrf
              <input type="number" class="allow form-control" name="porcentaje_materia" value="{{$monto->porcentaje_materia}}">
              Grupo
              <input autocomplete="off" type="text" class="allow form-control" name="grupo" list="grouplist" value="{{$al->grupo}}" placeholder="Escriba o seleccione un grupo existente">
              @php
                $col = DB::table('grupos')
                       ->select("grupo")
                       ->groupBy('grupo')
                       ->get();
              @endphp
              <datalist id="grouplist">
                @foreach ($col as $grupo)
                  <option value="{{$grupo->grupo}}">
                @endforeach
              </datalist>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
          @if (isset($al->curp) && $al->curp != null)
            <div class="col-12">
                <label class="text-dark">CURP:</label>
                <input type="text" class="form-control allow" required name="curp" value="{{$al->curp}}" placeholder="AARJ940901RFTKSS03">
                <label class="text-dark">Fecha de nacimiento:</label>
                <input type="date" class="form-control allow" required name="fecha_nacimiento" value="{{$al->fecha_nacimiento}}" placeholder="09/01/1994">
                <label class="text-dark">Genéro biológico:</label>
                <div class="form-control disabled">
                  {{$al->genero_biologico == "M" ? "Mujer" : "Hombre"}}
                </div>
                <label class="text-dark">Fecha de inscripción:</label>
                <input type="text" class="form-control disabled" disabled name="fecha_inscripcion" value="{{$al->fecha_inscripcion}}" placeholder="09/01/1994">
                <label class="text-dark">Fecha de registro en secretaría:</label>
                <input type="date" class="form-control allow" name="fecha_registro" value="{{$al->fecha_registro}}" placeholder="09/01/1994">
                <div class="row">
                  <div class="col">
                    <label class="text-dark">Periodo:</label>
                    <select class="allow form-control" name="periodo_id">
                      @foreach (\App\periodos::where("sede_id",Auth::user()->sede->sede->id)->get() as $per)
                        <option {{$per->id == $al->periodo_id ? "selected" : ""}} value="{{$per->id}}">{{$per->periodo}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col">
                    <label class="text-dark">Grupo:</label>
                    <input type="text" class="allow form-control" name="grupo_" value="{{$al->grupo_ ? $al->grupo_ : "A"}}" placeholder="A">
                  </div>
                  <div class="col">
                    <label class="text-dark">Grado:</label>
                    @php
                    $grados = ["Primero","Segundo","Tercero","Cuarto",
                      "Quinto","Sexto","Séptimo","Noveno","Décimo","Onceavo","Doceavo"
                    ];
                    @endphp
                    <select class="allow form-control" name="grado" style="height:35px;">
                      @foreach ($grados as $key => $value)
                        <option {{($key+1) == $al->grado ? "selected" : ""}} value="{{$key+1}}">{{$value}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </form>

  </div>
  <div class="card-footer">
    <button type="button" class="btn btn-primary save">
      <i class="fas fa-save"></i>
      Guardar
    </button>

  </div>
</div>
@if (isset($al->datosacademicos) && $al->datosacademicos != null)
  <div class="card">
    <div class="card-body">
      <h6>Datos académicos</h6>
      <hr>
      <div class="row">
        <div class="col-md-4 text-center">
          <a target="_blank" href="/ver/{{md5($al->datosacademicos->file)}}">Ver documentación</a> /
          <a href="/descargar/{{md5($al->datosacademicos->file)}}">Descargar</a>
        </div>
        <div class="col-md-4 text-center">
          <b>Fecha de término:</b> {{$al->datosacademicos->fecha_termino}}
        </div>
        <div class="col-md-4 text-center">
          <b>Cédula:</b> {{$al->datosacademicos->cedula}}
        </div>
      </div>
    </div>
  </div>
@endif
@if (isset($al->iadicional) && $al->iadicional != null)
  <div class="card">
    <div class="card-body">
      <h6>Información adicional</h6>
      <hr>
      @php
        $columns = Schema::getColumnListing('iadicional');
        $ignore = ["id","alumno_id","created_at","updated_at"];
      @endphp
      <div class="row">
      @foreach ($columns as $key => $val)
        @php
          $_val = NULL;
          if($alumno->iadicional != null)
            eval("\$_val = \$alumno->iadicional->$val;");
          $_name = ucfirst($val);
          $_name = str_replace("_"," ",$_name);
        @endphp
        @if (!in_array($val,$ignore))
          <div class="col-md-4">
            <label for="">{{$_name}}</label>
            <input type="text" disabled name="{{$val}}" placeholder="{{$_name}}" class="form-control" value="{{$_val}}">
          </div>
          @else
            @if ($val == $ignore[1])
                <input type="hidden" name="{{$val}}" value="{{$alumno->id}}">
              @else
                <input type="hidden" name="{{$val}}" value="{{$_val}}">
            @endif
        @endif
      @endforeach
      </div>
    </div>
  </div>
@endif
@if (isset($al->facturacion) && $al->facturacion->razonsocial != "")
<div class="card">
  <div class="card-body" id="facturacion">
    <h6>Facturación</h6>
    <hr>
    <div class="row">
      <div class="col-md-3">
        <label for="">Tipo de persona</label>
        <div class="form-control">
          {{$al->facturacion->tipo_persona == "fisica" ? "Física" : "Moral"}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Uso CFDI</label>
        <div class="form-control">
          {{$al->facturacion->usocfdi == "gastosengeneral" ? "Gastos en general" : "Por definir"}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Correo electrónico</label>
        <div class="form-control">
          {{$al->facturacion->correo}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Razón social</label>
        <div class="form-control">
          {{$al->facturacion->razonsocial}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">RFC</label>
        <div class="form-control">
          {{$al->facturacion->rfc}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Código postal</label>
        <div class="form-control">
          {{$al->facturacion->codigopostal}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Número exterior</label>
        <div class="form-control">
          {{$al->facturacion->numeroexterior}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Número interior</label>
        <div class="form-control">
          {{$al->facturacion->numerointerior}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Estado</label>
        <div class="form-control">
          {{$al->facturacion->estado}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Alcaldia</label>
        <div class="form-control">
          {{$al->facturacion->alcaldia}}
        </div>
      </div>
      <div class="col-md-3">
        <label for="">Colonia</label>
        <div class="form-control">
          {{$al->facturacion->colonia}}
        </div>
      </div>
    </div>
  </div>
</div>
@endif
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <h6>Acciones</h6>
        <hr>
        <br>
        @if (isset($al->matricula))
            <a class="btn btn-primary" href="/alumnos/informacionadicional?cid={{base64_encode($al->matricula).($al->iadicional != null ? "&update=yes" : "")}}">
              <i class="fas fa-child"></i>
              {{$al->iadicional == null ? "Agregar" : "Editar"}} información adicional
             </a>
          @if (count($al->revoe->materias) > 0)
            <a class="btn btn-info" href="/alumnos/asignarmaterias?cid={{Request::get("cid")}}">
              <i class="fas fa-cubes"></i>
              Materias
            </a>
          @endif
          @if ($al->datosacademicos != null)
            <a class="btn btn-default disabled">
              <i class="fa fa-trash"></i>
              Eliminar datos académicos
            </a>
          @else
            <a class="btn btn-primary " href="/alumnos/datosacademicos?cid={{base64_encode($al->matricula)}}">
              <i class="fas fa-graduation-cap"></i> Datos académicos
            </a>
          @endif
        @endif
        <a href="/alumnos/nuevopago?cid={{urlencode(Request::get('cid'))}}{{Request::has("did")?"&did=".Request::get("did"):""}}" class="btn btn-success">
          <i class="fas fa-plus"></i>
          Nuevo pago
        </a>
        <a href="/alumnos/planes?cid={{urlencode(Request::get('cid'))}}" class="btn btn-info">
          <i class="fas fa-wallet"></i>
          Planes de pago
        </a>
        @if(\Auth::user()->nivel->name == "Administrador" || \Session::has("admin"))
          <a href="#" class="btn btn-danger delete">
            <i class="fas fa-trash"></i>
            Eliminar alumno
          </a>
          <form class="eliminarform" action="/alumnos/eliminar?cid={{urlencode(Request::get('cid'))}}" method="post">
            @csrf
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item">
        <button class="btn btn-default active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Pagos</button>
      </li>
      <li class="nav-item">
        <button class="btn btn-default" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Archivo de pagos eliminados</button>
      </li>
      <li class="nav-item">
        <button class="btn btn-default" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Archivo de pagos devueltos</button>
      </li>
    </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
      @php
        $pagos = \App\pagos::where("matricula",$al->clave_alumno)->orderBy("id","desc")->get();
        $pago_id = Request::get("pago") ?: "none";
        $deletes = [];
        $returnes = [];
        $o = 0;
      @endphp
      @if (count($pagos) > 0)
        <div id="pagos">
          <div class="col-md-12">
            <table data-page-length="20" class="table pagos table-striped">
              <thead>
                <th>Folio</th>
                <th>Plan</th>
                <th>Monto ({{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}})</th>
                <th>Pagos</th>
                <th>Cargos</th>
                <th>Comprobante</th>
                <th>Tipo de pago</th>
                <th>Fecha de pago</th>
                <th>Adicionales</th>
                <th>Acciones</th>
                <th>Eliminar</th>
                <th>Devolver</th>
              </thead>
              <tbody>
                @foreach ($pagos as $pago)
                  @php
                    if($pago->returned_at != NULL){
                      $returnes[$o++] = $pago;
                    }
                    if($pago->deleted_at != NULL){
                      $deletes[$o++] = $pago;
                    }
                  @endphp
                  @if ($pago->deleted_at == NULL && $pago->returned_at == NULL)
                  <tr>
                    <td>
                      {!!$pago_id != md5($pago->id) ? "" : '<i class="fas fa-arrow-circle-right text-success"></i>'!!}
                      {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                    </td>
                    <td>
                      @if ($pago->plan_pagos)
                          @if (Auth::user()->nivel->nivel == "Administrador" || \Session::has("admin"))
                            <a href="#delete{{$pago->id}}" id="delete{{$pago->id}}" class="unlink" cid={{md5($pago->id)}}>
                              <i class="fas fa-link"></i>
                            </a>
                            @else
                              <i class="fas fa-link"></i>
                          @endif
                        @else
                          <a href="#" cid='{{md5($pago->id)}}' class="asignarplan">
                            <i class="fas fa-unlink"></i>
                          </a>
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
                            {{$pago->metodo.$pago->extra}}
                          </div>
                      @endif
                    </td>
                    <td>
                      {!!($pago->document_id == 0 ? "Sin comprobante" : "<a target='_blank' href='/ver/".md5($pago->document_id)."'>Ver comprobante</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>Descargar</a>")!!}
                    </td>
                    <td>
                      {{$pago->clave}}
                    </td>
                    <td>
                      @if ($pago->estado == NULL)
                        <div class="editable" cid="{{md5($pago->id)}}">
                          <span time="{{\Carbon\Carbon::parse($pago->fecha_pago)->timestamp}}" class="fecha">{{\Carbon\Carbon::parse($pago->fecha_pago)->format("Y-m-d")}}</span>
                        </div>
                        @else
                          {{\Carbon\Carbon::parse($pago->fecha_pago)->format("Y-m-d")}}
                      @endif
                    </td>
                    <td>
                      <a href="#" onclick="javascript:event.preventDefault();Swal.fire('Concepto','{{empty($pago->concepto) ? "Sin concepto" : $pago->concepto}}','question')" class="pop">
                        Concepto
                      </a>
                      <a href="#" onclick="javascript:event.preventDefault();Swal.fire('Nota','{{empty($pago->nota) ? "Sin nota" : $pago->nota}}','question')" class="pop">
                        Nota
                      </a>
                    </td>
                    <td>
                      @php
                        $issede = isset($pago->sede) ? $pago->sede->sede->sede : "Sin sede";
                      @endphp
                      <a class="text-danger pointer descargar" href="#me{{$pago->id}}" id='me{{$pago->id}}'>
                        <div class="cid" val='{{md5($pago->id)}}'></div>
                        <div class="fecha" val='{{\Carbon\Carbon::parse($pago->fecha_pago)->format("Y-M-d")}}'></div>
                        <div class="monto" val='{{$amount->format($pago->monto)}}'></div>
                        <div class="monto2" val='{{$pago->monto}}'></div>
                        <div class="pago" val='{{$pago->clave}}'></div>
                        <div class="folio" val='{{mb_strtoupper(substr($pago->sede->sede->sede,0,2))}}-{{str_pad("".$pago->sede_folio,6,"0",STR_PAD_LEFT)}}'></div>
                        <div class="foliogen2" val='{{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}'></div>
                        <div class="foliogen" val='Solicita tu factura en: https://siin.mx/invoice/{{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}} o escanea el código QR.'></div>
                        <div class="sede" val='{{mb_strtoupper($issede)}}'></div>
                        <div class="grupo" val='{{mb_strtoupper($al->grupo)}}'></div>
                        <div class="folio_impreso" val='{{"FI: ".$pago->folio_impreso ?: 'NF--'}}'></div>
                        <div class="nombre" val='{{$al->clave_alumno}} - {{mb_strtoupper($al->nombre)}} {{mb_strtoupper($al->primer_apellido)}} {{mb_strtoupper($al->segundo_apellido)}}'></div>
                        <div class="nota" val='{{$pago->nota}}'></div>
                        <div class="concepto" val='{{$pago->concepto}}'></div>
                        <div class="divisa" val='{{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}}'></div>
                        Generar recibo de pago
                      </a>
                    </td>
                    <td>
                      @if ($pago->estado == NULL || $sede == "Administrador")
                        <span class="eliminar" val='{{$pago->id}}'><i class="fa fa-trash"></i> Eliminar</span>
                        <form class="eliminar{{$pago->id}}" action="/pagos/eliminar" method="post">
                          @csrf
                          <input type="hidden" class="razon" required name="razon">
                          <input type="hidden" name="cid" value="{{md5($pago->id)}}">
                        </form>
                      @endif
                  </td>
                  <td>
                    @if ($pago_id != "none" && (auth()->user()->nivel->name == "Administrador"))
                          <span class="devolver" val='{{$pago->id}}'><i class="fas fa-exchange-alt"></i> Devolver</span>
                          <form class="devolver{{$pago->id}}" action="/pagos/devolver" method="post">
                            @csrf
                            <input type="hidden" name="cid" value="{{md5($pago->id)}}">
                          </form>
                        @else
                          @if ($pago->conciliacion == NULL || ($pago->conciliacion != NULL && $pago->conciliacion->estado == NULL))
                            <span class="iniciardevol" val='{{$pago->id}}'><i class="fas fa-exchange-alt"></i> Iniciar devolución</span>
                            <form class="iniciardevol{{$pago->id}}" action="/pagos/iniciardevol" method="post">
                              @csrf
                              <input type="hidden" name="cid" value="{{md5($pago->id)}}">
                            </form>
                          @endif
                    @endif
                  </td>
                  </tr>
                  @php
                    $total_pagado += $pago->monto;
                  @endphp
                @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif
    </div>
    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
      @if (count($deletes) > 0)
        <div id="pagosdeletes">
          <div class="col-md-12">
            <table data-page-length="20" class="table pagosdel table-striped">
              <thead>
                <th>Folio</th>
                <th></th>
                <th>Monto ({{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}})</th>
                <th>Aplicación</th>
                <th>Comprobante</th>
                <th>Tipo de pago</th>
                <th>Fecha eliminado</th>
                <th>Concepto</th>
                <th>Razón</th>
                <th>Recibos</th>
              </thead>
              <tbody>
                @foreach ($deletes as $pago)
                  <tr>
                    <td>
                      {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                    </td>
                    <td>
                      @if ($pago->plan_pagos)
                          <a href="#delete{{$pago->id}}" id="delete{{$pago->id}}" class="unlink" cid={{md5($pago->id)}}>
                            <i class="fas fa-link"></i>
                          </a>
                        @else
                          <i class="fas fa-unlink"></i>
                      @endif
                    </td>
                    <td>
                        {{$amount->format($pago->monto)}}
                    </td>
                    <td>
                      {{$pago->created_at}}</br>
                        <small>
                          {{\Carbon\Carbon::parse($pago->created_at)->diffForHumans()}}
                        </small>
                    </td>
                    <td>
                      {!!($pago->document_id == 0 ? "Sin comprobante" : "<a target='_blank' href='/ver/".md5($pago->document_id)."'>Ver comprobante</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>Descargar</a>")!!}
                    </td>
                    <td>
                      {{$pago->clave}}
                    </td>
                    <td>{{$pago->deleted_at}}</br>
                      <small>
                        {{\Carbon\Carbon::parse($pago->deleted_at)->diffForHumans()}}
                      </small>
                    </td>
                    <td>
                      <button type="button" onclick="javascript:Swal.fire('Concepto','{{empty($pago->concepto) ? "Sin concepto" : $pago->concepto}}','question')" class="btn btn-secondary pop">
                        Concepto
                      </button>
                    </td>
                    <td>
                      <button type="button" onclick="javascript:Swal.fire('Razón de eliminación','{{empty($pago->razon) ? "Sin razón" : $pago->razon}}','question')" class="btn btn-secondary pop">
                        Razón
                      </button>
                    </td>
                    <td>
                      {{$pago->gen_count}}
                    </td>
                  </tr>
                @endforeach
              </tbody>
              </table>
          </div>
        </div>
      @endif
    </div>
    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
      @if (count($returnes) > 0)
        <div id="pagosreturnes">
          <div class="col-md-12">
            <table data-page-length="20" class="table pagosdel table-striped">
              <thead>
                <th>Folio</th>
                <th></th>
                <th>Monto ({{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}})</th>
                <th>Aplicación</th>
                <th>Comprobante</th>
                <th>Tipo de pago</th>
                <th>Fecha devolución</th>
                <th>Concepto</th>
                <th>Recibos</th>
              </thead>
              <tbody>
                @foreach ($returnes as $pago)
                  <tr>
                    <td>
                      {!!$pago_id != md5($pago->id) ? "" : '<i class="fas fa-arrow-circle-right text-success"></i>'!!}
                      {{\Carbon\Carbon::parse($pago->created_at)->format("Ym")}}{{$pago->id}}
                    </td>
                    <td>
                      @if ($pago->plan_pagos)
                          @if (auth()->user()->nivel->name == "Administrador")
                            <a href="#delete{{$pago->id}}" id="delete{{$pago->id}}" class="unlink" cid={{md5($pago->id)}}>
                              <i class="fas fa-link"></i>
                            </a>
                            @else
                                <i class="fas fa-link"></i>
                                @if ($pago->estado == 10)
                                  <i class="far fa-clock"></i>
                                @endif
                          @endif
                        @else
                          <i class="fas fa-unlink"></i>
                      @endif
                    </td>
                    <td>
                        {{$amount->format($pago->monto)}}
                    </td>
                    <td>
                      {{$pago->created_at}}</br>
                        <small>
                          {{\Carbon\Carbon::parse($pago->created_at)->diffForHumans()}}
                        </small>
                    </td>
                    <td>
                      {!!($pago->document_id == 0 ? "Sin comprobante" : "<a target='_blank' href='/ver/".md5($pago->document_id)."'>Ver comprobante</a> / <a target='_blank' href='/descargar/".md5($pago->document_id)."'>Descargar</a>")!!}
                    </td>
                    <td>
                      {{$pago->clave}}
                    </td>
                    <td>{{$pago->returned_at}}</br>
                      <small>
                        {{\Carbon\Carbon::parse($pago->returned_at)->diffForHumans()}}
                      </small>
                    </td>
                    <td>
                      <button type="button" onclick="javascript:Swal.fire('Concepto','{{empty($pago->concepto) ? "Sin concepto" : $pago->concepto}}','question')" class="btn btn-secondary pop">
                        Concepto
                      </button>
                    </td>
                    <td>
                      {{$pago->gen_count}}
                    </td>
                  </tr>
                @endforeach
              </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif
  </div>
  </div>
</div>
@if (count($al->materias_cursadas) > 0)
  <div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <h6>Control de materias</h6>
        <hr>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table data-page-length="10" class="table asignaturas">
          <thead>
            <th>Identificador</th>
            <th>Asignatura</th>
            <th>Clave de Asignatura</th>
            <th>Calficación</th>
            <th>Estado de pago</th>
          </thead>
          <tbody>
            @foreach ($al->materias_cursadas as $materia)
              @php
                $materia = (object) $materia;
                $cantidad++;
              @endphp
              <tr>
                <td>
                  {{$materia->asignatura_id}}
                </td>
                <td>
                  {{$materia->asignatura}}
                </td>
                <td>
                  {{$materia->asignatura_clave}}
                </td>
                <td>
                  {{$materia->calificacion}}
                </td>
                <td>

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





@endsection
@section('scripts')
  <script type="text/javascript">
      $(".asignaturas").DataTable(lang);
      $(".pagos").DataTable(lang);
      $(".pagosdel").DataTable(lang);
      $(".pagosret").DataTable(lang);
  </script>
  <script src="https://unpkg.com/pdf-lib@1.4.0"></script>
  <script src="https://unpkg.com/downloadjs@1.4.7"></script>
  <script src="/js/numero.js?rnd={{rand()}}"></script>
  <script type="text/javascript">
      $(".asignarplan").bind("click",function(){
        event.preventDefault();
        var planes = JSON.parse('{!!\App\alumnos_planes::with("plan")->where("disable",NULL)->where("matricula",$al->clave_alumno)->get()->toJson()!!}');
        var parent = $(this).parent();
        var cid_pago = $(this).attr("cid");
        var selector = $("<select>").addClass("form-control").css("width",200);
        $(this).remove();
        console.log(planes);
        var option = $("<option>").text("Selecciona");
        selector.append(option);
        $.each(planes,function(index,el){
          var option = $("<option>").val(el.id).text(el.plan.concepto+" de "+el.plan.monto+" a "+el.plan.plazo+(el.plan.plazo==1 ? " plazo" :" plazos"));
          selector.append(option);
        });

        parent.append(selector);

        selector.bind("change",function(){
          $(this).prop("disabled",true);
          ShowWaitNotify("Asignando plan");
          $.post("/planes/asignarpago",{
            "_token":"{{csrf_token()}}",
            "cid":cid_pago,
            "plan_id":$(this).val()
          },function(data){
            ShowSuccessNotify("Pago asignado");
            parent.empty();
            parent.append('<i class="fas fa-link"></i>');
          })
        });
      })
      $(".editable").bind("dblclick",function(){
        $(this).find(".fecha").addClass("d-none");
        let input = $("<input>").attr("type","date").addClass("form-control date");
        input.val($(this).find(".fecha").html());
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
            $.post("/pagos/acfecha?_token={{csrf_token()}}&cid="+cid+"&fecha="+fecha,function(data){
              ShowSuccessNotify("Información modificada");
              spamfecha.html(fecha).css({"color":"green"});
            });
          }
        });

        $(this).append(input);
      });
      $(".eliminar").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas eliminar este pago?',
          text: "Describe el motivo de eliminación del pago",
          input:"text",
          inputAttributes:{
            "style":"width:80%;",
            "placeholder": "Motivo ..."
          },
          inputLabel: "Motivo de eliminación",
          inputValidator: (value) => {
            if (!value) {
              return '¡Es necesario que proporciones una razón de eliminación!'
            }
          },
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(".razon").val($("#swal2-input").val());
            $(".eliminar"+$(this).attr("val")).submit();
          }
        });
      });
      $(".delete").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas eliminar el perfil completo del alumno?',
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(".eliminarform").submit();
          }
        });
      });
      $(".devolver").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas devolver el pago?',
          text: "Esta acción desvinculará los planes de pago activos, además si el pago esta conciliado, será marcado como devolución y no será tomado en cuenta para los cálculos finales.",
          showCancelButton: true,
          confirmButtonText: 'Continuar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(".devolver"+$(this).attr("val")).submit();
          }
        });
      });
      $(".iniciardevol").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas iniciar una devolución?',
          text: "Esta acción enviará una solicitud para dar de baja el pago seleccionado.",
          showCancelButton: true,
          confirmButtonText: 'Continuar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(".iniciardevol"+$(this).attr("val")).submit();
          }
        });
      });

      $(".unlink").bind("click",function(){
        $.post("/pagos/unlink?_token={{csrf_token()}}&cid="+$(this).attr("cid"),function(){
          location.reload();
        });
      })

      $(".save").bind("click",function(){
        Swal.fire({
          icon: 'warning',
          title: '¿Deseas guardar los cambios?',
          showCancelButton: true,
          confirmButtonText: 'Guardar',
        }).then((result) => {
          if (result.isConfirmed) {
            $(".alumnog").submit();
          }
        });
      });

      const { PDFDocument, StandardFonts, rgb } = PDFLib
      async function createPdf(th) {

        Swal.fire({
          icon: 'info',
          title: 'Generando recibo ...',
          showCloseButton: 'Aceptar'
        });

        $.post("/pagos/gencount?_token={{csrf_token()}}&cid="+$(th).find(".cid").attr("val"));

        const url = '/resources/Invoice.pdf'
        const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
        const pdfDoc = await PDFDocument.load(existingPdfBytes)
        const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
        const page = pdfDoc.getPages()[0]
        const page2 = pdfDoc.getPages()[1]
        const { width, height } = page.getSize()

        var urls = "https://siin.mx/invoice/"+$(th).find(".foliogen2").attr("val");
        const jpgUrl = 'https://chart.googleapis.com/chart?chs=75x75&cht=qr&chl='+urls+'&chld=L|1&choe=UTF-8'

        const jpgImageBytes = await fetch(jpgUrl).then((res) => res.arrayBuffer())

        const jpgImage = await pdfDoc.embedPng(jpgImageBytes)
        const jpgDims = jpgImage.scale(1)

        page.drawImage(jpgImage, {
          x: 480,
          y: 440,
          width: jpgDims.width,
          height: jpgDims.height,
          opacity: 0.75,
        })

        page.drawImage(jpgImage, {
          x: 480,
          y: 440-360,
          width: jpgDims.width,
          height: jpgDims.height,
          opacity: 0.75,
        })

        const fontSize = 10

        var col = 0;

        switch($(th).find(".pago").attr("val")){
          case "Tarjeta":
            col = 270;
          break;
          case "Efectivo":
            col = 170;
          break;
          case "Transferencia":
            col = 475;
          break;
        }

        const jpgUrl2 = '/resources/check.png'
        const jpgImageBytes2 = await fetch(jpgUrl2).then((res) => res.arrayBuffer())
        const jpgImage2 = await pdfDoc.embedPng(jpgImageBytes2)
        const jpgDims2 = jpgImage2.scale(1)

        page.drawImage(jpgImage2, {
          x: col,
          y: 612.5,
          width: jpgDims2.width,
          height: jpgDims2.height,
          opacity: 0.75,
        })

        page.drawImage(jpgImage2, {
          x: col,
          y: 612.5-360,
          width: jpgDims2.width,
          height: jpgDims2.height,
          opacity: 0.75,
        })

        var ts = [
          {t:"fecha",x:115,y:13.5},
          {t:"folio",x:470,y:7.6,c:rgb(1,0,0),s:12},
          {t:"monto",x:265,y:12.6},
          {t:"nombre",x:130,y:20.35},
          {t:"sede",x:110,y:22.45},
          {t:"grupo",x:355,y:22.45},
          {t:"foliogen",x:155,y:35.7},
          {t:"concepto",x:130,y:25.65},
        ];

        if($(th).find(".folio_impreso").attr("val").indexOf("NF--") == -1){
          ts.push(
            {
              t:"folio_impreso",
              x:425,
              y:10
            }
          );
        }

        ts.push({t:NumeroALetras($(th).find(".monto2").attr("val"),"{{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}}"),x:320,y:14.8,s:7,n:1});

        $.each(ts,function(i,e){
          var v = (e.n == undefined) ? $(th).find("."+e.t).attr("val") : e.t;
          var c = (e.c != undefined) ? e.c : rgb(0,0,0);
          var s = (e.s != undefined) ? e.s : 10;
          page.drawText(v, {
            x: e.x,
            y: height - e.y * fontSize,
            size: s,
            font: timesRomanFont,
            color: c,
          });
        });

        $.each(ts,function(i,e){
          var v = (e.n == undefined) ? $(th).find("."+e.t).attr("val") : e.t;
          var c = (e.c != undefined) ? e.c : rgb(0,0,0);
          var s = (e.s != undefined) ? e.s : 10;
          page.drawText(v, {
            x: e.x,
            y: 431 - e.y * fontSize,
            size: s,
            font: timesRomanFont,
            color: c,
          });
        });

        page.drawText($(th).find(".nota").attr("val"), {
          x: 52,
          y: 2 * fontSize,
          size: 10,
          font: timesRomanFont,
        });

        const pdfBytes = await pdfDoc.save()

        download(pdfBytes, $(th).find(".monto").attr("val")+$(th).find(".fecha").attr("val")+".pdf", "application/pdf");

        Swal.close();
      };

      $(function(){
        $(".descargar").bind("click",function(){
            createPdf(this);
        });
      });

  </script>
@endsection
