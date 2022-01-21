@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
@php
  $sede = \App\sedes::whereRAW("md5(id)='".Request::get('cid')."'")->first();
  $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
  $planes = $sede->planespago;
  $planes_id = [];
  $i = 0;
  foreach ($planes as $plan_sede) {
    $planes_id[$i++] = $plan_sede->plan_id;
  }
@endphp
<div class="card">
  <div class="card-body">
    <h3>Añadir plan de pago a <a href="/sede/lista">sede</a> </h3>
    <hr>
    <form class="input" action="/sedes/addplan" method="post">
      @csrf
      <div class="row">
        <div class="col-4">
          <label for="">Selecciona el plan de pago:</label>
          <input type="hidden" name="cid" value="{{$sede->id}}">
          <select style="padding-left:10px;" class="form-control allow" required name="planes">
            <option value="">Seleccione plan</option>
            @foreach (\App\planespago::whereNotIn("id",$planes_id)->get() as $plan)
              @if ($plan->disable == null)
                <option value="{{$plan->id}}">
                  {{\Carbon\carbon::parse($plan->created_at)->format("Y")}}{{$plan->id}}
                   - {{$plan->concepto}}
                </option>
              @endif
            @endforeach
          </select>
        </div>
        <div class="col-4">
          <br>
          <input class="btn btn-primary" type="submit" name="" value="Añadir">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <h3>Planes de pago {{$sede->sede}}</h3>
    <hr>
    <div class="row">
      @php
        $sede = \App\sedes::whereRAW("md5(id)='".Request::get("cid")."'")->first();
      @endphp
    </div>
      @if(count($planes) > 0)
        <table class="table table1">
          <thead>
            <th>Folio</th>
            <th>Concepto</th>
            <th>Monto</th>
            <th>Plazo</th>
            <th>Pago</th>
            <th></th>
          </thead>
          <tbody>
            @foreach ($planes as $plan_sede)
              @php
                $plan = $plan_sede->plan;
              @endphp
              <tr>
                @if ($plan != null)
                  <td>{{\Carbon\Carbon::parse($plan->created_at)->format("Y")}}{{$plan->id}}</td>
                  <td>{{$plan->concepto}} {!!$plan->disable ? "<span class='text-danger'>(Este plan se encuentra desactivado)</span>" : ""!!}</td>
                  <td>{{$amount->format($plan->monto)}}</td>
                  <td>{{$plan->plazo}}</td>
                  <td>{{$amount->format($plan->monto/$plan->plazo)}}</td>
                  @else
                    <td>NULL</td>
                    <td>Sin definir</td>
                    <td>Sin definir</td>
                    <td>Sin definir</td>
                    <td>Sin definir</td>
                @endif
                <td>
                  <form action="/sedes/delplan" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$plan_sede->id}}">
                    <button type="submit" class="btn btn-link">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
      <h3 class="text-center">
        <i class="fas fa-exclamation-triangle text-warning"></i> No hay planes de pago
      </h3>
    @endif
  </div>
</div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".table1").DataTable(lang);
  </script>
@endsection
