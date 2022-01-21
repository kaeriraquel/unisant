@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-header">
    <h4 class="card-title ">Alumnos estatales</h4>
    <p class="card-category">Sin planes</p>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 text-right">

      </div>
    </div>
      <table class="table alumnos" data-page-length='200'>
        <thead class=" text-primary">
          <tr>
            <th>
              Matricula
            </th>
            <th>
              Nombre
            </th>
            <th>
              Grupo
            </th>
            <th>
              Sede
            </th>
          </tr>
        </thead>
        <tbody>
          @php
            $data = \App\alumnosest::where("sede_id",\Auth::user()->sede->sede->id)->doesntHave("planespago")->get();
            $data2 = \App\nombres::where("sede_id",\Auth::user()->sede->sede->id)->doesntHave("planespago")->get();
          @endphp
          @foreach ($data as $al)
              <tr>
                <td>
                  {{$al->matricula}}
                </td>
                <td>
                  <a href="/alumnos/planes?cid={{base64_encode($al->matricula)}}">
                    {{$al->nombre_completo}}
                    {{$al->apat}}
                    {{$al->amat}}
                  </a>
                </td>
                <td>
                  {{$al->grupo ? $al->grupo->grupo : "Sin grupo"}}
                </td>
                <td>
                  {{$al->sede->sede}}
                </td>
              </tr>
          @endforeach
          @foreach ($data2 as $al)
              <tr>
                <td>
                  {{$al->matricula}}
                </td>
                <td>
                  <a href="/alumnos/planes?cid={{base64_encode($al->matricula)}}">
                    {{$al->nombre}}
                  </a>
                </td>
                <td>
                  {{$al->grupo ? $al->grupo->grupo : "Sin grupo"}}
                </td>
                <td>
                  {{$al->sede->sede}}
                </td>
              </tr>
          @endforeach
        </tbody>
      </table>
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".alumnos").DataTable(lang);
  </script>
@endsection
