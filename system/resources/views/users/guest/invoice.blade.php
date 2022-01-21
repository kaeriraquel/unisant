<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Recibo de pago UNISANT</title>
    <style>
      html,body {
        background-color:#BC783E;
        height:100%;
      }
      .container-fluid{
        background: none;
      }
    </style>
  </head>
  <body>
    <form action="/facturas/solicitarfactura" method="post">
      @csrf
      <input type="hidden" name="folio" value="{{substr($codigo,6)}}">
    <div class="container-fluid">
      <div class="row">
        <div class="col"></div>
        <div class="col-md-5 mt-5">
          <div class="card">
            <div class="card-body">
              @php
              $pago = \App\pagos::where("id",substr($codigo,6))->first();
              @endphp
              @if ($pago == null)
                <div class="row">
                  <div class="col p-3 text-center" style="background:#f6f6f6;">
                    El recibo de pago buscado, no existe.
                  </div>
                </div>
                @else
                  <input type="hidden" class="folio" value="{{$pago->id}}">
                  <div class="row">
                    <div class="col">
                      <table style="height:100%;width:100%;">
                        <tr>
                          <td style="vertical-align:center;">
                            <b>Folio:</b> {{$codigo}}
                          </td>
                        </tr>
                        @if ($pago->folio_impreso != null)
                          <tr>
                            <td style="vertical-align:center;">
                              <b>Folio impreso:</b> {{$pago->folio_impreso}}
                            </td>
                          </tr>
                        @endif
                      </table>
                    </div>
                    <div class="col-6">
                      <img src="{{asset("images/logo_largue.png")}}" class="img-fluid">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col p-3" style="background:#f6f6f6;"></div>
                  </div>
                  @php
                    $dis = isset($pago->factura) ? "disabled" : "";
                    $api_url = "https://plataformaunisant.mx/unisant/apiEstudy/externos/alumno/detalleAlumno.php";
                    $api_token = "4ba07dd78a8a6bc15844adebebffc342";

                    $url = $api_url."?token=$api_token&matricula=".$pago->matricula;
                    $ch = curl_init($url);

                    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $res = curl_exec($ch);
                    curl_close($ch);
                    $data = json_decode($res,true);

                    $tipo = "estatal";
                    if(isset($data["alumno"])){
                      $tipo = "federal";
                      $al = (object) $data["alumno"];
                      $al->codigopostal = $al->cp;
                      $al->numeroexterior = $al->num_ext;
                      $al->numerointerior = $al->num_int;
                      $al->usocfdi = "";
                      $al->tipo_persona = "";

                      $tox = \App\facturacion::where("folio_id",$pago->id)->first();
                      if ($tox != null) {
                        $al->razon_social = $tox->razon_social;
                        $al->rfc = $tox->rfc;
                        $al->codigopostal = $tox->codigopostal;
                        $al->numeroexterior = $tox->numeroexterior;
                        $al->numerointerior = $tox->numerointerior;
                        $al->estado = $tox->estado;
                        $al->alcaldia = $tox->alcaldia;
                        $al->colonia = $tox->colonia;
                        $al->email = $tox->correo;
                        $al->usocfdi = $tox->usocfdi;
                        $al->tipo_persona = $tox->tipo_persona;
                      }
                    } else {
                      $alumno = \App\alumnosest::where("matricula",$pago->matricula)->first();
                      $al = (object) [];
                      $al->clave_alumno = $alumno->matricula;
                      $al->nombre = $alumno->nombre_completo;
                      $al->primer_apellido = $alumno->apat;
                      $al->segundo_apellido = $alumno->amat;
                      $al->materias_cursadas  = [];
                      if (!isset($alumno->facturacion)) {
                        \App\facturacion::create(["alumno_id"=>$alumno->id]);
                      }
                      $al->razon_social = $alumno->facturacion->razonsocial;
                      $al->rfc = $alumno->facturacion->rfc;
                      $al->codigopostal = $alumno->facturacion->codigopostal;
                      $al->numeroexterior = $alumno->facturacion->numeroexterior;
                      $al->numerointerior = $alumno->facturacion->numerointerior;
                      $al->estado = $alumno->facturacion->estado;
                      $al->alcaldia = $alumno->facturacion->alcaldia;
                      $al->colonia = $alumno->facturacion->colonia;
                      $al->email = $alumno->facturacion->correo;
                      $al->usocfdi = $alumno->facturacion->usocfdi;
                      $al->tipo_persona = $alumno->facturacion->tipo_persona;
                    }

                    $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );

                    $labels = [
                      "Alumno $tipo" => $al->nombre." ".$al->primer_apellido." ".$al->segundo_apellido,
                      "Matrícula" => $al->clave_alumno,
                      "Sede" => $pago->sede->sede->sede,
                      "Monto" => $amount->format($pago->monto),
                      "Fecha de pago" => $pago->fecha_pago ?: $pago->created_at,
                    ];
                  @endphp
                  @foreach ($labels as $l => $c)
                    <div class="row m-1">
                      <div class="col">
                        <label for="">
                          <b>{{$l}}</b>
                        </label>
                      </div>
                      <div class="col">
                        <div class="">
                          {{$c}}
                        </div>
                      </div>
                    </div>
                  @endforeach
                  <div class="row">
                    <div class="col p-3" style="background:#f6f6f6;"></div>
                  </div>
                  <div class="row">
                    <div class="col">
                      @php
                      $labels = [
                        "Tipo de persona" => ["tipo_persona",["fisica"=>"Física","moral"=>"Moral"],$al->tipo_persona],
                        "Uso CFDI" => ["usocfdi",["gastosengeneral"=>"Gastos en general","pordefinir"=>"Por definir"],$al->usocfdi],
                        "Razón social" => ["razonsocial",($al->razon_social ?: mb_strtoupper($al->nombre." ".$al->primer_apellido." ".$al->segundo_apellido))],
                        "RFC" => ["rfc",$al->rfc],
                        "Correo electrónico fiscal" => ["correo",$al->email],
                        "line",
                        "Código postal" => ["codigopostal",$al->codigopostal],
                        "Número interior" => ["numerointerior",$al->numerointerior],
                        "Número exterior" => ["numeroexterior",$al->numeroexterior],
                        "Colonia" => ["colonia",$al->colonia],
                        "Alcaldia" => ["alcaldia",$al->alcaldia],
                        "Estado" => ["estado",$al->estado],
                      ];
                      @endphp
                      @foreach ($labels as $l => $c)
                        @if ($l=="line")
                          <div class="col p-1" style="background:#f6f6f6;"></div>
                          @else
                            <div class="row m-1">
                              <div class="col">
                                <label for="">
                                  <b>{{$l}}</b>
                                </label>
                              </div>
                              <div class="col">
                                @if (is_array($c[1]))
                                    <select {{$dis}} required class="form-control" name="{{$c[0]}}">
                                      <option value="">Selecciona</option>
                                      @foreach ($c[1] as $key => $value)
                                        <option {{($key==$c[2]) ? "selected" : ""}} value="{{$key}}">{{$value}}</option>
                                      @endforeach
                                    </select>
                                  @else
                                    <input {{$dis}} required placeholder="{{$l}}" class="form-control" type="text" name="{{$c[0]}}" value="{{$c[1]}}">
                                @endif
                              </div>
                            </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                  <div class="row">
                    <div class="col p-3" style="background:#f6f6f6;"></div>
                  </div>
                  <div class="row">
                    <div class="col text-center" style="max-width:120px;">
                      <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{"https://siin.mx/invoice/$codigo"}}&chld=L|1&choe=UTF-8" class="img-fluid">
                    </div>
                    <div class="col text-center" style="max-height:100px;min-height:100px;line-height:100px;">
                      <small>{{base64_encode("https://siin.mx/invoice/$codigo")}}</small>
                    </div>
                  </div>
                  @if (!isset($pago->factura))
                    <div class="row">
                      <div class="col p-3" style="background:#f6f6f6;"></div>
                    </div>
                    <div class="row">
                      <div class="col m-2 text-center">
                        <input type="checkbox" name="factura" id="factura" value="">
                        <label for="factura">
                          <small>Mi información es correcta y acepto emitir factura con ella.</small>
                        </label>
                      </div>
                    </div>
                  @endif
                  <div class="row">
                    <div class="col p-1" style="background:#f6f6f6;"></div>
                  </div>
                  <div class="row">
                    <div class="col"></div>
                    <div class="col-12 text-center">
                      <br>
                      @if (isset($pago->factura))
                          <div class="btn">
                            Factura solicitada <i class="fas fa-check-circle text-success"></i>
                          </div>
                        @else
                          {{-- <div class="solicitar">
                            <div class="btn btn-success factura">

                            </div>
                          </div> --}}
                          <input type="submit" class="btn btn-success" value="Solicitar factura">
                      @endif
                      <br>
                      <small>
                        <a href="mailto:{{$pago->sede->usuario->email}}">Hablar con mi asesor de sede</a>
                      </small>
                    </div>
                    <div class="col"></div>
                  </div>
              @endif
            </div>
          </div>
        </div>
        <div class="col"></div>
      </div>
      <br>
      <br>
      <div class="modal" style="height:100px;">
        <p>Para solicitar factura, confirma que tus datos son correctos. <br />Click para <a href="#" rel="modal:close">cerrar</a></p>
      </div>
    </div>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script type="text/javascript">
      $(function(){
        $(".factura2").bind("click",function(){
          if($("#factura").is(":checked")){
            $(".solicitar").empty();
            $(".solicitar").append($("<i>").addClass("fas fa-cog fa-spin"));
            var tk = "&_token={{csrf_token()}}";
            $.post("/facturas/solicitar?folio="+$(".folio").val()+tk,function(data){
              data = JSON.parse(data);
              $(".solicitar").empty();
              if(data.status != undefined){
                if(data.status == 1){
                  $(".solicitar").append("Factura solicitada ");
                  $(".solicitar").append($("<i>").addClass("fa fa-check-circle text-success"));
                  $(".rights").addClass("none");
                } else {
                  $(".solicitar").append($("<span>").addClass("text-danger").text("Ha sucedido un error :("));
                }
              } else {
                $(".solicitar").append($("<span>").addClass("text-danger").text("Ups, algo no funciona correctamente :("));
              }
            });
          } else {
            $(".modal").modal({
              fadeDuration: 100
            });
          }
        });
      });
    </script>
  </body>
</html>
