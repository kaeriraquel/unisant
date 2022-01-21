@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h4>Procesos de grado concluidos</h4>
    <hr>
    <table class="table table-stripe concluidos">
      <thead>
        <th>Matrícula</th>
        <th>Alumno</th>
        <th>Acta</th>
        <th>Certificado</th>
        <th>Grado</th>
      </thead>
      <tbody>
        @foreach (\App\titulos::where("avance","<>",100)->get() as $acta)
          @php
          $matricula = $acta->matricula;
          $nombre = \App\alumnosest::where("matricula",$matricula)->first();
          if($nombre == null){
            $nombre = \App\nombres::where("matricula",$matricula)->first();
          }
          $nombre_completo = $nombre->nombre ? $nombre->nombre : "$nombre->nombre_completo $nombre->apat $nombre->amat";
          @endphp
          <tr>
            <td>{{$acta->matricula}}</td>
            <td>
              <a class="results" href="/titulacion/view?cid={{base64_encode($matricula)}}">
                {{$nombre_completo}}
              </a>
            </td>
            <td>{{$acta->acta->avance}}%</td>
            <td>{{$acta->certificado->avance}}%</td>
            <td>{{$acta->avance}}%</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".concluidos").DataTable(lang);
  </script>
@endsection
