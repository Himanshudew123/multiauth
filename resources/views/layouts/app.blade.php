<!doctype html>
  <html lang="en">
    <head>
      <meta charset="utf-8" />
      <title>E-COME | 
      @isset($pageTitle)
      {{ $pageTitle }}  
      @else
      Dashboard
      @endisset
    </title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta name="title" content="Ecom|Admin" />
      <meta name="author" content="ColorlibHQ" />
      <meta name="description" content="" />
      <meta
        name="keywords"
        content="bootstrap 5, admin dashboard, charts, datatables"
      />
      
<!-- Select2 CSS & Bootstrap Theme -->
 <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
      <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/css/adminlte.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
      <!-- Fonts -->
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous"
      />

      <!-- Overlay Scrollbars -->
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
        crossorigin="anonymous"
      />

      <!-- Bootstrap Icons -->
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        crossorigin="anonymous"
      />

      <!-- AdminLTE CSS -->
      <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}" />
      @stack('styles')

      <!-- ApexCharts -->
      <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        crossorigin="anonymous"
      />

      <!-- Loader CSS -->
      <style>
        .loader-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.7); /* dark semi-transparent */
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    font-family: sans-serif;
    font-size: 16px;
  }

  .spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #ffffff;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }

      </style>
    </head>

    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
      <!-- Loader -->
      <div id="loader" class="loader-overlay">
    <div class="spinner"></div>
    <div class="loading-text">Loading...</div>
  </div>

      <!--begin::App Wrapper-->
      <div class="app-wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        
        <div class="container">
        @include('partials.breadcrumb')
        @yield('content')
        </div>
        @include('partials.footer')
      </div>

      <!-- Scripts -->
      <script
        src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"
      ></script>
      <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        crossorigin="anonymous"
      ></script>
      <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        crossorigin="anonymous"
      ></script>
      <script src="js/adminlte.js"></script>

      <script>
        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
          scrollbarTheme: 'os-theme-light',
          scrollbarAutoHide: 'leave',
          scrollbarClickScroll: true,
        };

        document.addEventListener('DOMContentLoaded', function () {
          const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
          if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
              scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
              },
            });
          }
        });
      </script>

      <!-- ApexCharts -->
      <script
        src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
        crossorigin="anonymous"
      ></script>

      <!-- Hide loader after page loads -->
      <script>
    window.addEventListener('load', function () {
      const loader = document.getElementById('loader');
      loader.style.opacity = '0';
      setTimeout(() => loader.style.display = 'none', 300);
    });
  </script>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/admin-lte@3/dist/js/adminlte.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  @yield('scripts')
    </body>
  </html>
