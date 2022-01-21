@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])
@section('content2')
  <div class="card">
    <div class="card-body">
      <h4>Acciones generales</h4>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <h3>Saldos a distribuir</h3>
          <table data-page-length="50" class="table pagos table-striped">
            <thead>
              <th>Sede</th>
              <th>Monto a distribuir sede (30%)</th>
              <th>Monto a distribuir rectoria (70%)</th>
              <th>Acciones</th>
            </thead>
            <tbody>
              @foreach (\App\sedes::all() as $se)
                @if(isset($se->sedex))
                  <tr>
                    <td>
                      {{$se->sede}}
                    </td>
                    <td>
                      @php
                        $total_ = 0;
                          foreach ($se->sedex->pagos as $_p) {
                            if($_p->estado == 1)
                              $total_ += $_p->monto;
                          }
                      @endphp
                      ${{money_format("%i",$total_*.3)}}
                    </td>
                    <td>
                      ${{money_format("%i",$total_*.7)}}
                    </td>
                    <td>
                      <form action="/pagos/distribuir?cid={{md5($se->sedex->id)}}" method="post">
                          @csrf
                          <input type="submit" class="btn btn-primary" value="Distribuir {{$se->sede}}">
                      </form>
                    </td>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".pagos").DataTable(lang);
  </script>
@endsection
