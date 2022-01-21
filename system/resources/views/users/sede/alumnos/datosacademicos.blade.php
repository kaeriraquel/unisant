@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  @php
    $alumno = \App\alumnosest::where("matricula",base64_decode(Request::get("cid")))->first();
  @endphp
<form action="/alumnos/academicos" method="post" enctype="multipart/form-data">
  <div class="card">
    <div class="card-body">
      <h3>Datos académicos</h3>
      <hr>
      <div class="row">
        <div class="col-md-4">
          <input type="hidden" name="id" value="{{$alumno->id}}">
          <label for="">Antecedente académico:</label>
          <input class="form-control" type="file" name="file">
          <label for="">Fecha de término de antecedente</label>
          <input class="form-control" type="date" name="fecha_termino" required placeholder="{{date("d/m/Y")}}">
          <label for="">Número de cédula profesional (Licenciatura y Maestría)</label>
          <input class="form-control" type="text" name="cedula" placeholder="090902993">
          @csrf
          <br>
          <input type="submit" class="btn btn-primary" value="Guardar">
        </div>
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
