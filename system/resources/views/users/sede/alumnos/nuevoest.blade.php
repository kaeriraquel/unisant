@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
<form action="/alumnos/guardarest" method="post" enctype="multipart/form-data">
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <h3>Nuevo alumno estatal</h3>
          <h4>Información básica</h4>
          <hr>
          @csrf
          <div class="row">
            <div class="col-md-6">
              <label class="text-dark">Nombre:</label>
              <div class="row">
                <div class="col">
                  <input type="text" class="allow form-control mayus" required name="nombre_completo" placeholder="David">
                </div>
                <div class="col">
                  <input type="text" class="allow form-control mayus" required name="apat" placeholder="Valdivia">
                </div>
                <div class="col">
                  <input type="text" class="allow form-control mayus" required name="amat" placeholder="Rios">
                </div>
              </div>
              <label class="text-dark">Matricula:</label>
              <input type="text" class="allow form-control" value="TEMP{{Date("YmdHs")}}" required name="matricula" placeholder="2021054005">
              <label class="text-dark">RVOE:</label>
              <select class="allow form-control" required name="revoe_id">
                <option value="">Seleccione</option>
                @if (Auth::user()->nivel->name == "Administrador")
                    @foreach (\App\revoes::all() as $revoe)
                      <option value="{{$revoe->id}}">{{$revoe->clave}} - {{$revoe->nombre}}</option>
                    @endforeach
                  @else
                    @foreach (\Auth::user()->sede->sede->revoes as $revoe)
                      <option value="{{$revoe->revoe->id}}">{{$revoe->revoe->clave}} - {{$revoe->revoe->nombre}}</option>
                    @endforeach
                @endif
              </select>
              <label class="text-dark">Sede:</label>
              {{-- <select class="allow form-control" required name="sede_id">
                <option value="">Seleccione</option>
                @if (Auth::user()->nivel->name == "Administrador")
                    @foreach (\App\sedes::all() as $sede)
                      <option value="{{$sede->id}}">{{$sede->sede}}</option>
                    @endforeach
                  @else
                    @foreach (\App\sedes::where("sede","<>","Administrador")->get() as $sede)
                      <option {{\Auth::user()->sede->sede->id == $sede->id ? "selected" : ""}} value="{{$sede->id}}">{{$sede->sede}}</option>
                    @endforeach
                @endif
              </select> --}}
              <input type="hidden" name="sede_id" value="{{\Auth::user()->sede->sede->id}}">
              <div class="form-control">
                {{\Auth::user()->sede->sede->sede}}
              </div>
              <label class="text-dark">Grupo</label>
              <input autocomplete="off" type="text" class="allow form-control" name="grupo" list="grouplist" placeholder="Escriba o seleccione un grupo existente">
              @php
                $col = DB::table('grupos')
                       ->select("grupo")
                       ->groupBy('grupo')
                       ->get();
              @endphp
              <datalist id="grouplist">
                @foreach ($col as $grupo)
                  <option value="{{$grupo->grupo}}">
                @endforeach
              </datalist>
            </div>
            <div class="col-md-6">
              <label class="text-dark">CURP:</label>
              <input type="text" class="allow form-control" required name="curp" placeholder="AARJ940901RFTKSS03">
              <label class="text-dark">Fecha de nacimiento:</label>
              <input type="date" class="allow form-control" required name="fecha_nacimiento" placeholder="09/01/1994">
              <label class="text-dark">Genéro biológico:</label>
              <select class="allow form-control" required name="genero_biologico">
                <option value="">Seleccione</option>
                <option value="H">Hombre</option>
                <option value="M">Mujer</option>
              </select>
              <label class="text-dark">Fecha de inscripción:</label>
              <input type="date" class="allow form-control" required name="fecha_inscripcion" value="{{date("d/m/Y")}}" placeholder="09/01/1994">
              <label class="text-dark">Fecha de registro en secretaría (Opcional):</label>
              <input type="date" class="allow form-control" name="fecha_registro" placeholder="09/01/1994">
            </div>
            <hr>
          </div>
          <div class="collapse" id="collapseExample">
            <div class="row">
              <div class="col-md-12">
                <hr>
                <h4>Facturación<h6>(Opcional)</h6></h4>
                <hr>
              </div>
              <div class="col-md-6">
                <label class="text-dark">Razón social:</label>
                <input type="text" class="allow form-control" name="razonsocial" placeholder="Razón social">
                <label class="text-dark">RFC:</label>
                <input type="text" class="allow form-control" name="rfc" placeholder="RFC">
                <label class="text-dark">Código postal:</label>
                <input type="text" class="allow form-control" name="codigopostal" placeholder="Código postal">
                <label class="text-dark">Número exterior:</label>
                <input type="text" class="allow form-control" name="numeroexterior" placeholder="Número exterior">
              </div>
              <div class="col-md-6">
                <label class="text-dark">Número interior:</label>
                <input type="text" class="allow form-control" name="numerointerior" placeholder="Número interior">
                <label class="text-dark">Estado:</label>
                <input type="text" class="allow form-control" name="estado" placeholder="Estado">
                <label class="text-dark">Alcaldía/municipio:</label>
                <input type="text" class="allow form-control" name="alcaldia" placeholder="Alcaldía/municipio">
                <label class="text-dark">Colonia:</label>
                <input type="text" class="allow form-control" name="colonia" placeholder="Colonia">
              </div>
            </div>
          </div>
            <hr>
          <input type="submit" class="btn btn-primary" value="Guardar">
          <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample" class="btn btn-info">
            Agregar información fiscal
          </a>
          <a href="/alumnos/listaest" class="btn btn-success">Lista de alumnos</a>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
  <script type="text/javascript">
    $(".mayus").bind("keyup",function(){
      this.value = this.value.toUpperCase();
    });
  </script>
@endsection
