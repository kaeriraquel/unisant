@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $alumno = \App\alumnosest::where("matricula",base64_decode(Request::get("cid")))->first();
  @endphp
<form action="/alumnos/informacionadicional" method="post" enctype="multipart/form-data">
  <div class="card">
    <div class="card-body">
      <h3>Información adicional</h3>
      <h6>{{$alumno->matricula}} {{$alumno->nombre_completo." ".$alumno->apat." ".$alumno->amat}}</h6>
      <hr>
          <input type="hidden" name="id" value="{{$alumno->id}}">
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
              @if (!in_array($val,$ignore) || (\Request::has("update") && $val == $ignore[1]))
                <div class="col-md-6">
                  @if ($val!=$ignore[1])
                    <label for="">{{$_name}}</label>
                  @endif
                  <input type="{{$val==$ignore[1]?"hidden":"text"}}" name="{{$val}}" placeholder="{{$_name}}" class="allow form-control" value="{{$_val}}">
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
            <hr>
          @csrf
          <br>
          <input type="submit" class="btn btn-primary" value="{{\Request::has("update")?"Actualizar":"Guardar"}}">
          <a href="/alumnos/pagos?cid={{\Request::get("cid")}}" class="btn btn-danger">Cancelar</a>
      </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
  <script src="https://unpkg.com/pdf-lib@1.4.0"></script>
  <script src="https://unpkg.com/downloadjs@1.4.7"></script>
  <script type="text/javascript">

      const { PDFDocument, StandardFonts, rgb } = PDFLib
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
