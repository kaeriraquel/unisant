<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de información</title>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('material') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('material') }}/img/favicon.png">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ asset('material') }}/css/material-dashboard.css?v=2.1.2" rel="stylesheet" />
    <link href="{{ asset('material') }}/demo/demo.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.24/datatables.min.css"/>

    <link href="{{asset("css/custom.css?r=".Rand())}}" rel="stylesheet" />
    <link href="{{asset("css/context.css?r=".Rand())}}" rel="stylesheet" />
    <link href="{{asset("css/fastpop.css?x=".Rand())}}" rel="stylesheet" />

    @yield('styles')
    </head>
    <body class="{{ $class ?? '' }}">
      <div class="notify"></div>
      @auth
      <div class="hide2" id="rmenu">
        @php
          $icon = "fas fa-angle-right";
        @endphp
        <a href="/home" class="list-group-item list-group-item-action">
          <i class="iconito text-light {{$icon}}"></i>
          Inicio
        </a>
        @isset($menu)
          @else
            @if (auth()->user()->nivel->name == "Administrador")
              @php
                $menu = [
                  "Sedes" => "/sede/lista",
                  "Usuarios" => "/user",
                  "Distribuciones" => "/pagos/distribuciones"
                ];
              @endphp
            @endif
        @endisset
        @isset($menu)
            @foreach ($menu as $key => $value)
              @php
                $url = is_array($value) ? $value[0] : $value;
                $clase = is_array($value) ? $value[1] : "";
                $coin = [
                  "nuevo"=>"fas fa-file",
                  "editar"=>"fas fa-edit",
                  "eliminar"=>"fa fa-trash",
                  "regresar"=>"fas fa-undo-alt"
                ];
                // foreach($coin as $k => $v){
                //   if(strstr(strtolower($key),strtolower($k))){
                //     $icon = $v;
                //     break;
                //   }
                // }

              @endphp
              <a href="{{$url}}" class="{{$clase}} list-group-item list-group-item-action">
                <i class="iconito text-light {{$icon}}"></i>
                {{$key}}
              </a>
            @endforeach
        @endisset
        @if (\Session::has("admin") && auth()->user()->nivel->name != "Administrador")
          <a href="javascript:$('#rollback').submit()" class="list-group-item list-group-item-action">
            <i class="iconito text-light {{$icon}}"></i>
            Salir de este usuario
          </a>
        @endif
      </div>
      @endauth
        @auth()
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            @include('layouts.page_templates.auth')
        @endauth
        @guest()
            @include('layouts.page_templates.guest')
        @endguest

        <!--   Core JS Files   -->
        <script src="{{ asset('material') }}/js/core/jquery.min.js"></script>
        <script src="{{ asset('material') }}/js/core/popper.min.js"></script>
        <script src="{{ asset('material') }}/js/core/bootstrap-material-design.min.js"></script>
        <script src="{{ asset('material') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
        <script src="{{ asset('js') }}/lang.js?r={{rand()}}"></script>

        <!-- Plugin for the momentJs  -->

        <script src="{{ asset('material') }}/js/plugins/moment.min.js"></script>
        <!--  Plugin for Sweet Alert -->
        <script src="{{ asset('material') }}/js/plugins/sweetalert2.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Forms Validations Plugin -->
        <script src="{{ asset('material') }}/js/plugins/jquery.validate.min.js"></script>
        <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
        <script src="{{ asset('material') }}/js/plugins/jquery.bootstrap-wizard.js"></script>
        <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
        <script src="{{ asset('material') }}/js/plugins/bootstrap-selectpicker.js"></script>
        <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
        <script src="{{ asset('material') }}/js/plugins/bootstrap-datetimepicker.min.js"></script>
        <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
        <script src="{{ asset('material') }}/js/plugins/jquery.dataTables.min.js"></script>
        <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
        <script src="{{ asset('material') }}/js/plugins/bootstrap-tagsinput.js"></script>
        <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
        <script src="{{ asset('material') }}/js/plugins/jasny-bootstrap.min.js"></script>
        <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
        <script src="{{ asset('material') }}/js/plugins/fullcalendar.min.js"></script>
        <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
        <script src="{{ asset('material') }}/js/plugins/jquery-jvectormap.js"></script>
        <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
        <script src="{{ asset('material') }}/js/plugins/nouislider.min.js"></script>
        <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
        <!-- Library for adding dinamically elements -->
        <script src="{{ asset('material') }}/js/plugins/arrive.min.js"></script>
        <!-- Chartist JS -->
        <script src="{{ asset('material') }}/js/plugins/chartist.min.js"></script>
        <!--  Notifications Plugin    -->
        <script src="{{ asset('material') }}/js/plugins/bootstrap-notify.js"></script>
        <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
        <script src="{{ asset('material') }}/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
        <!-- Material Dashboard DEMO methods, don't include it in your project! -->
        <script src="{{ asset('material') }}/demo/demo.js"></script>
        <script src="{{ asset('material') }}/js/settings.js"></script>
        <script src="{{ asset('/js/context.js?r='.Rand()) }}"></script>
        <script src="{{ asset('/js/fastpop.js?r='.Rand()) }}"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.24/datatables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>

        <script type="text/javascript">
            $(document).ready(function(){
              @if (session('status'))
                  ShowSuccessNotify("{{\Session::pull("status")}}");
              @endif

              @if (session('error'))
                    ShowErrorNotify("{{\Session::pull("error")}}");
              @endif

              setTimeout(function(){
                document.getElementsByClassName("content")[0].style="overflow-y:scroll;";
              },3000);
            });
        </script>
        <script type="text/javascript">
          $(()=>{
            $(".table-striped").addClass("table-hover");
            $("input[type=search]").addClass("form-control allow");
          });
        </script>
        @yield('scripts')
        @stack('js')
    </body>
</html>
