@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <div class="clearfix">
      <div class="float-left">
        <h3>RVOEs</h3>
      </div>
      <div class="float-right">
        <br>
        <a href="/controlescolar/nuevo" class="btn btn-sm btn-primary">Nuevo RVs</a>
      </div>
    </div>
    <hr>
      <table class="table table-striped">
        <thead class=" text-primary">
          <tr>
            <th>
              Folio
            </th>
            <th>
              Nombre
            </th>
            <th>
              Clave
            </th>
            <th>
              Desc
            </th>
        </tr>
      </thead>
        <tbody>
            @foreach (\App\revoes::all() as $r)
              <tr>
                <td>{{\Carbon\Carbon::parse($r->created_at)->format("Y")}}{{$r->id}}</td>
                <td>
                  <a rel="tooltip" href="/controlescolar/nuevo?cid={{md5($r->id)}}" data-original-title="" title="">
                    {{$r->nombre}}
                  </a>
                </td>
                <td>
                  {{$r->clave}}
                </td>
                <td>
                  {{$r->descr}}
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
    $(".table").DataTable(lang);
  </script>
@endsection
