@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
  $planes = \App\alumnos_planes::where("matricula",base64_decode(Request::get("cid")))->where("disable","=",NULL)->get();
  @endphp
  @if (count($planes) > 0)
    <form action="/pagos/guardar" method="post" enctype="multipart/form-data">
      @else
    <form class="" action="index.html" method="post">
  @endif
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <h3>Nuevo pago</h3>
          <hr>
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col">
                  Monto a pagar ({{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}}):
                </div>
                <div class="col">
                  Cargo extra o descuento: <a href="#" class="question"><i class="fas fa-question-circle"></i></a>
                </div>
                <div class="col-2">
                  Método:
                </div>
              </div>
            </div>
            <div class="col-md-12">
              @php
              $menu = [
                "Regresar" => "/alumnos/pagos?cid=".urlencode(Request::get('cid')),
              ];
              $sede = (isset(Auth::user()->sede)) ? auth()->user()->sede->sede->sede: "Administrador";
              $individual = Request::has("did") ? NULL : \App\sedes::where("sede",$sede)->first();;
              if($individual == null){
                $individual = \App\sedes::find(Request::get("did"));
              }
              $sede_id = $individual->id;
              $url = $individual->individual."&matricula=".base64_decode(Request::get("cid"));
              $ch = curl_init($url);

              curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
              curl_setopt($ch, CURLOPT_HEADER, 0);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

              $res = curl_exec($ch);
              curl_close($ch);
              $data = json_decode($res,true);

              if(isset($data["response"])){
                $al = (object) $data["response"];
              } else {
                $alumno = \App\alumnosest::where("matricula",base64_decode(Request::get("cid")))->first();
                $al = (object) [];
                $al->clave_alumno = $alumno->matricula;
                $al->nombre = $alumno->nombre_completo;
                $al->primer_apellido = $alumno->apat;
                $al->segundo_apellido = $alumno->amat;
              }

              $desc = \App\montos::where("matricula",base64_decode(Request::get("cid")))->first()->porcentaje_materia;
              $total = $desc;

              $conceptospago = [];
              $allowance = [];
              $l = 0;
              foreach ($planes as $pl) {
                if($pl->plan->conceptopago != null && $pl->disable == null){
                  $conceptospago[$l++] = $pl->plan->conceptopago->id;
                  $allowance[$l++] = $pl->plan->conceptopago->allow;
                }
              }
              @endphp
              <div class="row">
                <div class="col">
                  <input type="number" step="0.01" name="monto" class=" allow form-control" placeholder="0.00" value="{{$total}}">
                </div>
                <div class="col">
                  <input type="number" step="0.01" name="extra" class="allow form-control" placeholder="0.00" placeholder="Cargo extra o descuento">
                </div>
                <div class="col-2">
                  <select class="form-control allow" style="height:35px;" name="metodo">
                    <option value="$">$</option>
                    <option value="%">%</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="row">
              <div class="col-md-12">
                <br><br>
                <br>
                Comprobante de pago (Sí lo hay):
              </div>
              <div class="col-md-12">
                <input type="file" class="form-control allow" name="file">
              </div>
          </div>
        </div>
        <div class="col-md-6">
        <div class="row">
          <div class="col-md-12">
            Alumno:
          </div>
          <div class="col-md-12">
            <input type="text" disabled class="disabled form-control" value="{{$al->nombre}} {{$al->primer_apellido}} {{$al->segundo_apellido}}">
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
              Método de pago
            </div>
            <div class="col-md-12">
              <input class="clave" type="radio" name="clave" required id="credito" value="Tarjeta">
              <label for="credito">Tarjeta</label>
              <input class="clave" type="radio" name="clave" required id="credito2" value="Efectivo">
              <label for="credito2">Efectivo</label>
              <input class="clave" type="radio" name="clave" required id="credito3" value="Transferencia">
              <label for="credito3">Trasferencia</label>
            </div>
        </div>
        <div class="row pas" style="display:none;">
            <div class="col-md-12">
              Pasarela:
            </div>
            <div class="col-md-12">
              <select class="form-control allow pasarelas" name="pasarela_id">

              </select>
            </div>
        </div>
      </div>
      <div class="col-md-6">
        Fecha de pago:
        <input type="date" class="form-control allow" required name="fecha_pago" placeholder="d/m/yyyy">
      </div>
      <div class="col-md-6">
        Tipo de pago:
        <select class="form-control allow concepto" required name="concepto">
          <option value="">Seleccione</option>
          @foreach (Auth::user()->sede->sede->conceptos as $con)
            @if (isset($con->concepto))
              <option cid="{{$con->concepto->id}}" value="{{$con->concepto->concepto}}">{{$con->concepto->concepto}}</option>
            @endif
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        Folio impreso (Sí lo hay):
        <input type="text" class="form-control allow" name="folio_impreso" placeholder="Folio impreso (Sí lo hay)">
        <div class="d-none">
          Cantidad de pagos incluidos:
          <input type="number" value="1" min="1" max="40" step="1" class="form-control allow cantidad_pagos" name="cantidad_pagos">
        </div>
      </div>
      <div class="col-md-6">
        Concepto:
        <input name="conceptoadicional" placeholder="Pago parcial, Pago de materia, Pago 1 de 3 ... etc." class="form-control allow" />
        <div class="d-none">
          Plan de pago relacionado con el pago:
          <select class="form-control allow planes" name="plan_id">

          </select>
        </div>
      </div>
      <div class="col-12">
        Nota:
        <textarea name="nota" class="form-control allow" placeholder="Nota de pago ..."></textarea>
      </div>
      </div>
    </div>
    <br>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col">
          @csrf
          @if (count($planes) > 0)
            <div class="clearfix">
              <div class="float-left">
                <input type="submit" class="btn btn-primary" value="Guardar">
              </div>
              <div class="float-right">
                <a href="javascript:history.back();" class="btn btn-link text-danger">
                    Cancelar
                </a>
              </div>
            </div>
            @else
              <br>
              <div class="alert alert-warning">
                <div class="clearfix">
                  <div class="float-left">
                    Necesitas al menos un plan de pagos activo para asignar pagos.
                  </div>
                  <div class="float-right">
                    <a href="javascript:history.back();" >
                        Cancelar
                    </a>
                  </div>
                </div>
              </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
  <script src="https://unpkg.com/pdf-lib@1.4.0"></script>
  <script src="https://unpkg.com/downloadjs@1.4.7"></script>
  <script type="text/javascript">
      $(".question").bind("click", () => {
        Swal.fire("Cargo extra o descuento","Escriba el cargo extra ej: 9.00 o un descuento ej: -9.00, sí desea utilizar un porcentaje seleccione % en lugar de $.","question");
      });

      $(".clave").bind("change",function(){
        $.post("/pagos/getpasarelas?_token={{csrf_token()}}&forma="+$(this).val(),function(data){
          $(".pasarelas").empty();
          var data = JSON.parse(data);
          if(data.length > 0){
            $(".pasarelas").append($("<option>").text("Selecciona").val(""));
            $.each(data,function(i,e){
              $(".pasarelas").append($("<option>").val(e.id).text(e.name));
            });
            $(".pas").fadeIn();
            $(".pasarelas").prop("required",true);
          } else {
            $(".pas").fadeOut();
            $(".pasarelas").removeAttr("required");
          }
        });
      });

      const conceptos = [
        @foreach($conceptospago as $con)
          {{$con}},
        @endforeach
      ];

      const allowance = [
        @foreach($allowance as $al)
          {{$al}},
        @endforeach
      ];

      const planesdata = JSON.parse('{!!$planes!!}');
      const { PDFDocument, StandardFonts, rgb } = PDFLib

      $(".concepto").bind("change",function(){
        let selId = $(this).find(":selected").attr("cid");
        let planes = $(".planes");
        let allow = $(".cantidad_pagos");
        planes.empty();
        if(conceptos.includes(parseInt(selId))){
          $.each(planesdata,function(i,e){
            if(e.plan.conceptopago.id == selId){
              planes.append($("<option>").val(e.id).text(e.plan.concepto+" de "+e.plan.monto+" a "+e.plan.plazo+(e.plan.plazo==1 ? " plazo" :" plazos")));
            }
          });
          planes.parent().removeClass("d-none");
          allow.parent().parent().removeClass("d-none");
          allow.prop("required",true);
        } else {
          planes.parent().addClass("d-none");
          allow.parent().parent().addClass("d-none");
          allow.prop("required",false);
        }
      });

      async function createPdf() {

        const url = '/resources/inv_edu0023.pdf'
        const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
        const pdfDoc = await PDFDocument.load(existingPdfBytes)
        const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
        const page = pdfDoc.getPages()[0]
        const { width, height } = page.getSize()

        const fontSize = 10
        var xd = new Date().toLocaleString('es-MX', { hour12: true });
        page.drawText(xd, {
          x: 70,
          y: height - 19.1 * fontSize,
          size: fontSize,
          font: timesRomanFont,
          color: rgb(0,0,0),
        });
        const pdfBytes = await pdfDoc.save()

        download(pdfBytes, "pdf-lib_creation_example.pdf", "application/pdf");
      };

      $(function(){
        $(".download").bind("click",function(){
            createPdf();
        });
      });

  </script>
@endsection
