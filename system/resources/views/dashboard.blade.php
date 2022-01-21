@php
  $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY );
@endphp
@extends('layouts.app', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])
  @section('content')
    <div class="content">
      <div class="container-fluid">
        @if (Request::get("nivel") == "Administrador" && isset($pend))
        <div class="row">
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-success card-header-icon">
                <div class="card-icon">
                  <span class="material-icons">
                    account_balance
                  </span>
                </div>
                <p class="card-category">Pagos recolectados por las sedes</p>
                <h3 class="card-title">${{$pend}}</h3>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">date_range</i> Último corte
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-danger card-header-icon">
                <div class="card-icon">
                  <i class="material-icons">info_outline</i>
                </div>
                <p class="card-category">Pagos pendientes</p>
                <h3 class="card-title">${{$paga}}</h3>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">local_offer</i>
                  <a href="/sede/distribuir">
                    Revisar distribución
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
              <div class="card-header card-header-info card-header-icon">
                <div class="card-icon">
                  <span class="material-icons">
                    account_balance_wallet
                  </span>
                </div>
                <p class="card-category">Distribuido</p>
                <h3 class="card-title">${{$dist}}</h3>
              </div>
              <div class="card-footer">
                <div class="stats">
                  <i class="material-icons">update</i> Actualizado
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
        <div class="row">
          <div class="col-md-12">
            @yield('content2')
            @yield('content3')
          </div>
        </div>
      </div>
    </div>
  @endsection

  @push('js')
    <script>
      $(document).ready(function() {
        // Javascript method's body can be found in assets/js/demos.js
        md.initDashboardPageCharts();
      });
    </script>
  @endpush
