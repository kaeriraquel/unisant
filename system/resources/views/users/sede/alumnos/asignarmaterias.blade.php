@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $menu = [
      "Regresar" => "/alumnos/pagos?cid=".urlencode(Request::get('cid')),
    ];
  @endphp
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          <h3>Materias</h3>
          <p>Añadir materias a un alumno permite crear un Kardex de calificaciones más adelante.</p>
        </div>
        <div class="float-right">
          <a href="/alumnos/pagos?cid={{Request::get("cid")}}">Regresar</a>
        </div>
      </div>
      <hr>
      @php
        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
        $alumno = \App\alumnosest::where("matricula",base64_decode(Request::get("cid")))->first();
      @endphp


      <form action="/alumnos/addmateria" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col">
            @csrf
            <input type="hidden" name="cid" value="{{md5($alumno->id)}}">
            <label class="text-dark" for="">Añadir materias:</label>
            <select class="allow form-control" required name="materia_id" style="padding-left:10px;">
              <option value="">Selecciona un materia</option>
              @if (count($alumno->revoe->materias) > 0)
                @foreach ($alumno->revoe->materias as $materia)
                  <option value="{{$materia->id}}">{{$materia->name}}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col">
            <label class="text-dark" for="">
              Calificación:
            </label>
            <input class="form-control allow cal" name="calificacion" max="10" min="0" type="number" value="-1">
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <label class="text-dark" for="">Periodo</label>
            <br>
            <input id="fecha_p" type="radio" name="periodo_type" value="fecha">
            <label for="fecha_p">Fecha</label>
            <span style="display:inline-block;width:20px;"></span>
            <input id="periodo_p" checked type="radio" name="periodo_type" value="periodo">
            <label for="periodo_p">Periodo</label>
            <select class="form-control allow" id="periodo_selector" name="periodo_id" required>
              <option value="">Seleccionar</option>
              @foreach (\App\periodos::where("sede_id",\Auth::user()->sede->sede->id)->where("deleted_at",NULL)->get() as $per)
                <option value="{{$per->id}}">{{$per->periodo}}</option>
              @endforeach
            </select>
            <input type="date" id="periodo_date" class="d-none form-control allow" value="">
          </div>
          <div class="col-6 calificacion d-none">
            <label style="margin-top:34px;" for="">Tipo de reprobada</label>
            <select class="form-control allow periodo" name="reprobada_id">
              <option value="">Seleccionar</option>
              @foreach (\App\tiposdereprobadas::all() as $rep)
                <option value="{{$rep->id}}">{{$rep->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <br>
            <input type="submit" class="btn btn-primary btn-sm" value="Añadir materias">
          </div>
        </div>
      </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-left">
            <h3>Kardex</h3>
            <p>Puedes generar un documento de Kardex con las materias aprobadas disponibles a continuación:
              <ul>
                <li>Primero selecciona todas las materias que deses añadir en el documento.</li>
                <li>Presiona generar Kardex.</li>
              </ul>
            </p>
          </div>
          <div class="float-right">

          </div>
        </div>
        <hr>
        <h4>Información del alumno</h4>
        <br>
        <div class="row">
          <div class="col">
            <label for="">Matrícula</label>
            <div class="form-control matricula">{{$alumno->matricula}}</div>
          </div>
          <div class="col">
            <label for="">Nombre completo</label>
            <div class="form-control nombre">{{$alumno->nombre_completo}} {{$alumno->apat}} {{$alumno->amat}}</div>
          </div>
          <div class="col">
            <label for="">RVOE</label>
            <div class="form-control rvoe_clave">{{$alumno->revoe->clave}}</div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <label for="">Carrera</label>
            <div class="form-control carrera">{{$alumno->revoe->nombre}}</div>
          </div>
          <div class="col">
            <label for="">Grupo</label>
            <div class="form-control grupo">{{$alumno->grupo->grupo}}</div>
          </div>
          <div class="col">
            <label for="">Sede</label>
            <div class="form-control sede">{{$alumno->sede->sede}}</div>
          </div>
          <input type="hidden" class="folio" value="{{\Carbon\Carbon::parse($alumno->created_at)->format("Ym")}}{{$alumno->id}}">
        </div>
        <hr>
        <table class="table" id="kardex" data-page-length="80">
          <thead>
            <th>
              <input type="checkbox" class="elcheck" id="elcheck" value="true">
            </th>
            <th>
              Folio
            </th>
            <th>
              Materia
            </th>
            <th>
              Clave
            </th>
            <th>
              Periodo
            </th>
            <th>
              Calificación
            </th>
            <th>
              Créditos
            </th>
            <th>
              Estado
            </th>
          </thead>
          <tbody>
            @foreach ($alumno->materias->where("estado","<>",NULL) as $mat)
              <tr>
                <td>
                  <input type="checkbox" class="loschecks" value="{{$mat->id}}">
                </td>
                <td>
                  {{\Carbon\carbon::parse($mat->created_at)->format("Y").$mat->id}}
                </td>
                <td>
                  {{$mat->materia->name}}
                </td>
                <td>
                  {{$mat->materia->clave}}
                </td>
                <td>
                  @php
                  if(is_numeric($mat->periodo_id)){
                    echo $mat->periodo->periodo;
                  }  else {
                    try {
                      echo \Carbon\carbon::parse($mat->periodo_id)->format("Y-m-d");
                    } catch (\Exception $e) {
                      echo "Fecha/periodo no válido";
                    }

                  }
                  @endphp
                </td>
                <td class="{{$mat->calificacion <= 5 ? "text-danger" : ""}}">
                  {{$mat->calificacion}}
                  {{$mat->calificacion <= 5 ? "(".$mat->reprobada->name.")" : ""}}
                </td>
                <td>
                  {{$mat->materia->creditos}}
                </td>
                <td>
                  {{$mat->estado == NULL ? "En validación" : ($mat->estado == 1 ? "Validado" : "Rechazado")}}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <hr>
        <div class="clearfix">
          <div class="foliogen" val='Revisa tu Kardex en: https://siin.mx/kardex/{{$alumno->matricula}} o escanea el código QR.'></div>
          <div class="float-left">
            <label for="">Fecha de expedición:</label>
            <input type="date" class="allow form-control expedicion">
          </div>
          <div class="float-right">
            <br>
            <button type="button" class="btn btn-primary btn-sm generar">
              Generar documento
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-left">
            <h3>Materias</h3>
            <small>de {{$alumno->nombre_completo}}</small>
            <p>Requieren aprobación de administrador escolar.</p>
          </div>
          <div class="float-right">
            <a href="/alumnos/pagos?cid={{base64_encode($alumno->matricula)}}">Regresar</a>
          </div>
        </div>
        <hr>
        @if(count($alumno->materias->where("estado",NULL)) > 0)
        <table class="table table-striped materias">
          <thead>
            <th>
              Folio
            </th>
            <th>
              Clave
            </th>
            <th>
              Materia
            </th>
            <th>
              Calificación
            </th>
            <th>
              Periodo
            </th>
            <th>
              Estado
            </th>
            <th>
              Acciones
            </th>
          </thead>
          <tbody>
            @foreach ($alumno->materias->where("estado",NULL) as $mat)
                <tr>
                  <td>
                    {{\Carbon\carbon::parse($mat->created_at)->format("Y").$mat->id}}
                  </td>
                  <td>
                    {{$mat->materia->clave}}
                  </td>
                  <td>
                    {{$mat->materia->name}}
                  </td>
                  <td class="{{$mat->calificacion <= 5 ? "text-danger" : ""}}">
                    {{$mat->calificacion}} {{$mat->calificacion <= 5 ? "(".$mat->reprobada->name.")" : ""}}
                  </td>
                  <td>
                    @php
                    if(is_numeric($mat->periodo_id)){
                      echo $mat->periodo->periodo;
                    }  else {
                      try {
                        echo \Carbon\carbon::parse($mat->periodo_id)->format("Y-m-d");
                      } catch (\Exception $e) {
                        echo "Fecha/periodo no válido";
                      }

                    }
                    @endphp
                  </td>
                  <td>
                    {{$mat->estado == NULL ? "En validación" : ($mat->estado == 1 ? "Aprobado" : "Rechazado")}}
                  </td>
                  <td>
                    @if ($mat->estado != 1)
                      <a href="#" class="del" cid={{md5($mat->id)}}>
                        <i class="fa fa-trash"></i>
                        Eliminar</a>
                    @endif
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
            ¿Sabias que  ... las materias solo pueden ser eliminadas si se encuentran en validación?
          </h4>
        @endif
      </div>
    </div>


@endsection
@section('scripts')
  <script src="https://unpkg.com/pdf-lib@1.4.0"></script>
  <script src="https://unpkg.com/downloadjs@1.4.7"></script>
  <script type="text/javascript">
    $("#kardex").DataTable(lang);
    $(".elcheck").bind("click",function(){
      $("input[type='checkbox']").prop('checked', $(this).is(':checked'));
    });

    $("#fecha_p").bind("click",function(){
      $("#periodo_selector").addClass("d-none");
      $("#periodo_selector").removeAttr("required");
      $("#periodo_selector").removeAttr("name");
      $("#periodo_date").removeClass("d-none");
      $("#periodo_date").prop("required",true);
      $("#periodo_date").attr("name","periodo_id")
    });
    $("#periodo_p").bind("click",function(){
      $("#periodo_date").addClass("d-none");
      $("#periodo_date").removeAttr("required");
      $("#periodo_date").removeAttr("name");
      $("#periodo_selector").removeClass("d-none");
      $("#periodo_selector").prop("required",true);
      $("#periodo_selector").attr("name","periodo_id")
    });

    $(".materias").DataTable(lang);
    $(".cal").bind("change",function(){
      if($(this).val() <= 5){
        $(".calificacion").removeClass("d-none");
        $(".periodo").attr("required","");
      } else {
        $(".calificacion").addClass("d-none");
        $(".periodo").removeAttr("required");
      }
    });
    $(".del").bind("click",function(){
      Swal.fire({
        icon: 'warning',
        title: '¿Deseas eliminar la materia seleccionada?',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
      }).then((result) => {
        if (result.isConfirmed) {
          ShowWaitNotify("Eliminando");
          $(this).find("i").removeClass("fa fa-trash");
          $(this).find("i").addClass("fas fa-cog fa-spin");
          let e = $(this);
          $.post("/alumnos/delmateria?cid="+$(this).attr("cid")+"&_token={{csrf_token()}}",function(data){
            console.log(e.parent().parent().parent().parent().remove());
            ShowSuccessNotify("Materia eliminada")
          });
        }
      });
    });
    $(".generar").bind("click", () => {
      let checks = $(".loschecks:checked");
      if(checks.length > 0){
        if($(".expedicion").val() != ""){
          ShowWaitNotify("Generando documento");
          createPdf(checks);
        } else {
          ShowErrorNotify("Selecciona la fecha de expedición");
        }
      } else {
        ShowErrorNotify("Seleccione al menos un elemento de la lista");
      }
    });
    const { PDFDocument, StandardFonts, rgb } = PDFLib
    async function createPdf(checks) {
        //$.post("/pagos/gencount?_token={{csrf_token()}}&cid="+$(th).find(".cid").attr("val"));

        const url = '/resources/kardex_pdf.pdf'
        const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
        const pdfDoc = await PDFDocument.load(existingPdfBytes)
        const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
        const page = pdfDoc.getPages()[0]
        const { width, height } = page.getSize()

        var urls = "https://siin.mx/kardex/"+$(".matricula").text();
        const jpgUrl = 'https://chart.googleapis.com/chart?chs=75x75&cht=qr&chl='+urls+'&chld=L|1&choe=UTF-8'

        const jpgImageBytes = await fetch(jpgUrl).then((res) => res.arrayBuffer())

        const jpgImage = await pdfDoc.embedPng(jpgImageBytes)
        const jpgDims = jpgImage.scale(1.5)

        page.drawImage(jpgImage, {
          x: 32.5,
          y: 52,
          width: jpgDims.width,
          height: jpgDims.height,
          opacity: 0.75,
        })


        const fontSize = 10

        var col = 0;

        let al = 30;
        let anchos = [-25,160,230,360, 470];
        let promsum = 0;
        for(let i = 0; i < checks.length; i++){
          let node = $(checks[i]).parent().parent().find("td");
            for(let j = 0; j < anchos.length; j++){
              let c = rgb(0,0,0);
              let text = "";
              let a = 0;
              if(node.hasClass("text-danger") && j+2 == 5)
              {
                c = rgb(1,0,0);
                text = $(node[j+2]).text().replace(/(\r\n|\n|\r)/gm, "").replace(/\s/g, '');
                a = anchos[j]+50;
              } else {
                c = rgb(0,0,0);
                text = $(node[j+2]).text().replace(/(\r\n|\n|\r)/gm, "");
                a = anchos[j];
              }

              if (j+2 == 5) {
                let val = parseInt(text.replace( /^\D+/g, ''));
                promsum += val;
              }

              page.drawText(text, {
                x: a,
                y: height - al * fontSize,
                size: 12,
                font: timesRomanFont,
                color:c
              });
            }
          al += 3;
        }

        let prom = (promsum/checks.length) + "";

        var ts = [
          {t:$(".expedicion").val(),x:520,y:9,c:rgb(0,0,0),s:12,n:""},
          {t:"nombre",x:215,y:14.3,c:rgb(0,0,0),s:12},
          {t:"matricula",x:140,y:17.3,c:rgb(0,0,0),s:12},
          {t:"rvoe_clave",x:430,y:17.3,c:rgb(0,0,0),s:12},
          {t:"carrera",x:125,y:20.3,c:rgb(0,0,0),s:12},
          {t:"grupo",x:105,y:23.4,c:rgb(0,0,0),s:12},
          {t:"sede",x:345,y:23.4,c:rgb(0,0,0),s:12},
          {t:$(".foliogen").attr("val"),x:155,y:60.7,n:""},
          {t:prom,x:530,y:64,c:rgb(0,0,0),s:12,n:""},
        ];

        $.each(ts,function(i,e){
          var v = (e.n == undefined) ? $("."+(e.t)).text().toUpperCase() : e.t;
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



        const pdfBytes = await pdfDoc.save()

        download(pdfBytes,"kardex.pdf", "application/pdf");
      }
  </script>
@endsection
