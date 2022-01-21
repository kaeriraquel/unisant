<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  @php
    $mn = [
      "Usuarios" => [
        ["Perfil de usuario","/profile"],
        ["Lista de usuarios","/user"]
      ],
      "Alumnos" => [
        ["Solicitud de bajas","/alumnos/solicitudes"],
        ["Solicitud de facturas","/facturas/lista"],
        ["Todos los alumnos","/alumnos/listaest"],
      ],
      "Sede" => [
        ["Lista de sedes","/sede/lista"],
        ["Configuraciones","/sede/configuraciones"],
        ["Conceptos","/requerimientos/lista"],
      ],
      "Control escolar" => [
        ["RVOEs","/controlescolar/lista"],
        ["Importar calificaciones","/controlescolar/importarmaterias"],
        ["Importar alumnos","/controlescolar/importaralumnos"],
        ["Periodos","/controlescolar/periodos"],
        ["Tipos de materia","/controlescolar/tiposdematerias"],
        ["Tipos de reprobadas","/controlescolar/tiposdereprobadas"],
        ["Estados del alumno","/controlescolar/estadosdelalumno"]
      ],
      "Pagos" => [
        ["Planes de pago","/pagos/planesdepago"],
        ["Conceptos de pago","/pagos/conceptosdepago"],
        ["Divisas de pago","/pagos/divisas"],
        ["Distribuciones","/pagos/distribuciones"],
        ["Pasarelas","/pagos/pasarelas"],
      ],
      "Académicos" => [
        ["Materias","/academicos/listamaterias"]
      ]
    ];
  @endphp
  <div class="logo">
    <a href="/" class="simple-text logo-normal">
      <img src="{{asset("images/logo.png")}}" class="img" style="height:50px;">
    </a>
  </div>
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link bg-animation-shiny" href="{{ route('home') }}">
          <i class="material-icons">dashboard</i>
            <p>Panel de control</p>
        </a>
      </li>
      @foreach ($mn as $iname => $ivalue)
        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#{{$iname}}" aria-expanded="{{isset($menucommand) && $menucommand == str_replace(" ","",strtolower($iname)) ? "true" : "false"}}">
            <p>
              <span class="material-icons">
                folder_shared
              </span>
              {{$iname}}
              <b class="caret"></b>
            </p>
          </a>
          <div class="collapse {{isset($menucommand) && $menucommand == str_replace(" ","",strtolower($iname)) ? "show" : ""}}" id="{{$iname}}">
            <ul class="nav">
              @foreach ($ivalue as $it)
                <li class="nav-item {{(isset($itemcommand) && ("/$menucommand/$itemcommand") == $it[1] ? "active" : "")}}">
                  <a class="nav-link" href="{{$it[1]}}">
                    <span class="sidebar-mini"> {{substr($it[0],0,2)}} </span>
                    <span class="sidebar-normal">
                      {{$it[0]}}
                    </span>
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </li>
      @endforeach
    </ul>
  </div>
</div>
