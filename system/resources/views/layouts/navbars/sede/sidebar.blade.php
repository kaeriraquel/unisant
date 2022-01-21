<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
  <div class="logo">
    <a href="/" class="simple-text logo-normal">
      <img src="{{asset("images/logo.png")}}" class="img" style="height:50px;">
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
          <i class="material-icons">dashboard</i>
            <p>Panel de control</p>
        </a>
      </li>
      <li class="nav-item {{ ($activePage == 'profile' || $activePage == 'user-management') ? ' active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#laravelExample2" aria-expanded="false">
          <p>
            <span class="material-icons">
              folder_shared
            </span>
            Alumnos federales
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="laravelExample2">
          <ul class="nav">
            <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
              <a class="nav-link" href="/alumnos/lista">
                <span class="sidebar-mini"> LA </span>
                <span class="sidebar-normal">
                  Lista de alumnos
                </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == 'user-management' ? ' active' : '' }}">

            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item {{ ($activePage == 'profile' || $activePage == 'user-management') ? ' active' : '' }}">
        <a class="nav-link" data-toggle="collapse" href="#laravelExample" aria-expanded="false">
          <p>
            <span class="material-icons">
              folder_shared
            </span>
            Alumnos estatales
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="laravelExample">
          <ul class="nav">
            <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
              <a class="nav-link" href="/alumnos/listaest">
                <span class="sidebar-mini"> LAE </span>
                <span class="sidebar-normal">
                  Lista de alumnos
                </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
              <a class="nav-link" href="/alumnos/nuevoest">
                <span class="sidebar-mini"> NAE </span>
                <span class="sidebar-normal">
                  Nuevo alumno
                </span>
              </a>
            </li>
            <li class="nav-item{{ $activePage == 'user-management' ? ' active' : '' }}">

            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#l3" aria-expanded="false">
          <p>
            <span class="material-icons">
              snippet_folder
            </span>
            Documentación oficial
            <b class="caret"></b>
          </p>
        </a>
        <div class="collapse" id="l3">
          <ul class="nav">
            <li class="nav-item{{ $activePage == 'profile' ? ' active' : '' }}">
              <a class="nav-link" href="/sede/videos">
                <span class="sidebar-mini">
                  <i class="fab fa-youtube text-danger"></i>
                </span>
                <span class="sidebar-normal">
                  Videos
                </span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      {{-- <li class="nav-item{{ $activePage == 'table' ? ' active' : '' }}">
        <a class="nav-link" href="{{ route('table') }}">
          <i class="material-icons">content_paste</i>
            <p>
              //
            </p>
        </a>
      </li> --}}

    </ul>
  </div>
</div>
