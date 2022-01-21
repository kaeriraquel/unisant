@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          <h3>Lista de contenidos</h3>
        </div>
        <div class="float-right">
          <br>
          <a href="/academicos/nuevamateria" class="btn btn-primary btn-sm">
            Nuevo
          </a>
        </div>
      </div>
      <hr>
      <table class="table table-striped materias">
        <thead>
          <th>Folio</th>
          <th>Nombre</th>
          <th>Créditos</th>
          <th>Eliminar</th>
        </thead>
        <tbody>
          @foreach (\App\listamaterias::all() as $mat)
            <tr>
              <td>{{$mat->id}}</td>
              <td>{{$mat->name}}</td>
              <td>{{$mat->creditos}}</td>
              <td>
              <form method="POST" action="/materias/borrar">
              @csrf
              <input type="hidden" value="{{$mat->id}}" name="id">
              <button type="submit" class="btn btn-link"><i class="fa fa-trash text-danger mr-2"></i>Eliminar</button>
              </form>
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
    $(".materias").DataTable(lang);
  </script>
@endsection
