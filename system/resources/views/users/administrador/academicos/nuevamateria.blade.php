@extends('dashboard', ['activePage' => 'profile', 'titlePage' => "__('User Profile')"])

@section('content2')
  <div class="card">
    <div class="card-body">
      <div class="clearfix">
        <div class="float-left">
          <h3>Nueva materia</h3>
        </div>
        <div class="float-right">
          <br>
          <a href="/academicos/listamaterias">
            Regresar
          </a>
        </div>
      </div>
      <hr>
      <form action="/materias/addmateria" method="post">
        @csrf
        <div class="row">
          <div class="col">
            <label for="">Nombre de materia:</label>
            <input type="text" required class="form-control allow" name="name" placeholder="Nombre de la materia" value="">
          </div>
        </div>
          <div class="row">
            <div class="col">
              <label>Contenido</label>
              <textarea name="contenido" id="contenido1">

              </textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <hr>
            <input type="submit" class="btn btn-primary" name="" value="Guardar">
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
@section('scripts')
  <script src="https://cdn.tiny.cloud/1/4eh5se8bzh2rwh4i26sh1a582xzigey103wfcd1h7smr5czs/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init(
      {
        selector:'#contenido1',
        plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        toolbar_mode: 'floating',
      }
    );
</script>
@endsection
