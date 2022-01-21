@include('layouts.navbars.navs.guest')
<div class="wrapper wrapper-full-page">
  <div class="page-header login-page header-filter" filter-color="black" style="height: 100%;
  background: radial-gradient(ellipse at bottom, #1b2735 0%, #090a0f 100%);
  overflow: hidden;" data-color="purple">
    @yield('content')
    @include('layouts.footers.guest')
  </div>
</div>
