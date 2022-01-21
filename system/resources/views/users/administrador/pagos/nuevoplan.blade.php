@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<div class="card">
  <div class="card-body">
    <h3>Nuevo plan de pagos</h3>
    <hr>
    <p>
      El plan de pagos necesita 4 elementos para calcularse:
      <ul>
        <li>Concepto: Factor descriptivo para ayudar a identificar el plan.</li>
        <li>Concepto relacionado: Es el concepto de pago al que el plan reaccionará y aplicará montos.</li>
        <li>Monto total: es el monto total esperado a recuperar por el servicio activo del alumno.</li>
        <li>Pagos: Es la cantidad sobre la cual serán dividos el monto total.</li>
      </ul>
    </p>
    <hr>
      <form class="form" action="/pagos/plandepago" method="post">
        @csrf
        <div class="row">
          <div class="col">
            <label for="">
              Concepto del plan:
            </label>
            <input class="form-control enable allow" type="text" name="concepto" placeholder="Concepto del plan" required>
            <label for="">Concepto relacionado el pago</label>
            <select required class="form-control allow" required name="concepto_id">
              <option value="">Selecione</option>
              @foreach (\App\conceptospago::all() as $con)
                <option value="{{$con->id}}">{{$con->concepto}}</option>
              @endforeach
            </select>
            <label for="">Monto total:</label>
            <input class="form-control enable allow" type="text" name="monto" placeholder="Monto total a cubrir por el plan">
            <label for="">Pagos:</label>
            <input class="form-control enable allow" type="number" min="1" name="plazo" value="1">
          </div>
        </div>
      </form>
      <hr>
      <a href="/pagos/planesdepago" class="btn btn-danger">
        <i class="fas fa-ban"></i>
        Cancelar
      </a>
      <a href="#" class="submitb btn btn-primary">
        <i class="fa fa-save"></i>
        Guardar
      </a>
    </div>
  </div>
@endsection
@section('scripts')
  <script type="text/javascript">
      $(".submitb").bind("click",function(){
        $(".form").submit();
      });
  </script>
@endsection
