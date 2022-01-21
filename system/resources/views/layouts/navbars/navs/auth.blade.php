<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
  <div class="container-fluid">
    <div class="navbar-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col">
            <h4 class="text-uppercase">
              Dashboard
              {{auth()->user()->nivel->name}}
            </h4>
            @if (count(auth()->user()->accesos) > 0)
              <small>
                <a href="/accesos/lista">
                  Ver otra sede
                </a>
              </small>
            @endif
            @if (session()->has("icomefrom"))
              <small>
                <form action="/sedes/retireaccess" method="post">
                  @csrf
                  <button class="btn btn-primary" type="submit">
                    Volver a ser {{\App\User::whereRAW("md5(id)='".session()->get("icomefrom")."'")->first()->email}}
                  </button>
                </form>
              </small>
            @endif
          </div>
        </div>
      </div>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
    <span class="sr-only">Toggle navigation</span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    <span class="navbar-toggler-icon icon-bar"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end">
      {{-- <form class="navbar-form">
        <div class="input-group no-border">
        <input type="text" value="" class="form-control" placeholder="Buscar...">
        <button type="submit" class="btn btn-white btn-round btn-just-icon">
          <i class="material-icons">search</i>
          <div class="ripple-container"></div>
        </button>
        </div>
      </form> --}}
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('home') }}">
            <i class="material-icons">home</i>
            <span class="font-weight-bold text-success">
              {{auth()->user()->sede->sede->div ? auth()->user()->sede->sede->div->divisa : "MXN"}}
            </span>
            <p class="d-lg-none d-md-block">
              Estadisticas
            </p>
          </a>
        </li>
        @php
          $noti = 0;
          if(\Session::has("admin") && auth()->user()->nivel->name != "Administrador")
            $noti++;
          $fact = \App\facturas::where("status",null)->count();
          if($fact > 0)
            $noti++;
          $tf = 0;
          if($tf > 0)
            $noti++;
          $npd = \App\pagos::where("estado",10)->where("returned_at","<>",NULL)->count();
          if ($npd > 0)
            $noti++;
        @endphp
        <li class="nav-item dropdown">
          <a class="nav-link" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">notifications</i>
            <span class="{{$noti > 0 ? "" : "d-none"}} notification">{{$noti}}</span>
            <p class="d-lg-none d-md-block">
              Notificaciones
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
            @if (\Session::has("admin") && auth()->user()->nivel->name != "Administrador")
              @php
                $u = \App\User::whereRAW("md5(id)='".\Session::get("admin")."'")->first();
              @endphp
              <form id="rollback" action="/HomeController/l{{$u->nivel->nivel == "Administrador" ? "og" : "admin"}}?cid={{md5($u->id)}}" method="post">
                @csrf
                <input type="submit" class="dropdown-item" value="Regresar a ser {{$u->name}} ({{$u->sede->sede->sede}})">
              </form>
            @endif
            @if ($fact > 0)
              <a href="/facturas/lista" class="dropdown-item">
                  Hay  {{$fact}}  solicitud{{$fact == 1 ? "" : "es"}} de factura pendiente{{$fact == 1 ? "" : "s"}}
              </a>
            @endif
            @if ($tf > 0)
              <a href="/alumnos/solicitudes" class="dropdown-item">
                  Hay  {{$tf}}  solicitud{{$tf == 1 ? "" : "es"}} de baja pendiente{{$tf == 1 ? "" : "s"}}
              </a>
            @endif
            @if ($npd > 0)
              <a href="/alumnos/solicitudes" class="dropdown-item">
                  Hay  {{$npd}}  solicitud{{$npd == 1 ? "" : "es"}} de devolución pendiente{{$npd == 1 ? "" : "s"}}
              </a>
            @endif
            @if ($noti == 0)
              <div class="dropdown-item">
                  <i class="fas fa-exclamation-triangle text-warning"></i> No hay notificaciones pendientes
              </div>
            @endif
        </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">person</i>
            {{auth()->user()->name}} <small>by {{auth()->user()->sede->sede->sede}}</small>
            <p class="d-lg-none d-md-block">
              Cuenta
            </p>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
            <a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a>
            <a class="dropdown-item" href="#">Configuración</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Salir</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
