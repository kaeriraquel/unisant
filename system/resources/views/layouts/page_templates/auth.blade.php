<div class="wrapper ">
  @include('layouts.navbars.'.(Request::get('nivelpath') ?? strtolower(Auth::user()->nivel->name)).'.sidebar')
  <div class="main-panel">
    @include('layouts.navbars.navs.auth')
    @yield('content')
    @include('layouts.footers.auth')
  </div>
</div>
