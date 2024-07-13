
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Stellar Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="{{ asset('manager-assets') }}/vendors/css/vendor.bundle.base.css">

    @stack('styles')
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('manager-assets') }}/css/style.css" <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('manager-assets') }}/images/favicon.png" />
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:{{ asset('manager-assets') }}/partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-brand-wrapper d-flex align-items-center">
          <a class="navbar-brand brand-logo" href="{{ route('manager.dashboard') }}">
            FetishOnly
          </a>
          <a class="navbar-brand brand-logo-mini" href="{{ route('manager.dashboard') }}">FetishOnly</a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center flex-grow-1">
          <h5 class="mb-0 font-weight-medium d-none d-lg-flex">Welcome Creator dashboard!</h5>
          <ul class="navbar-nav navbar-nav-right ml-auto">
            <li class="nav-item dropdown d-none d-xl-inline-flex user-dropdown">
              <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle ml-2" src="{{ auth()->user()->avatar }}" alt="Profile image"> <span class="font-weight-normal"> {{ auth()->user()->name }} </span></a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="{{ auth()->user()->avatar }}" alt="Profile image">
                  <p class="mb-1 mt-3">{{ auth()->user()->name }}</p>
                  <p class="font-weight-light text-muted mb-0">{{ auth()->user()->email }}</p>
                </div>
                <a onclick="event.preventDefault();document.getElementById('logout-form').submit()" class="dropdown-item"><i class="dropdown-item-icon icon-power text-primary"></i>Sign Out</a>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:{{ asset('manager-assets') }}/partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="profile-image">
                  <img class="img-xs rounded-circle" src="{{ auth()->user()->avatar }}" alt="profile image">
                  <div class="dot-indicator bg-success"></div>
                </div>
                <div class="text-wrapper">
                  <p class="profile-name">{{ auth()->user()->name }}</p>
                  <p class="designation">{{ auth()->user()->role->display_name }}</p>
                </div>
              </a>
            </li>
            <li class="nav-item nav-category">
              <span class="nav-link">Dashboard</span>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('manager.dashboard') }}">
                <span class="menu-title">Upload Media</span>
                <i class="icon-screen-desktop menu-icon"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{ route('manager.medias') }}">
                <span class="menu-title">Media List</span>
                <i class="icon-screen-desktop menu-icon"></i>
              </a>
            </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
          </div>

          <footer class="footer">
            <br>
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © {{ date('Y') }}</span>
              {{-- <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a href="https://www.bootstrapdash.com/bootstrap-admin-template/" target="_blank">Bootstrap admin template</a> from Bootstrapdash.com</span> --}}
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('manager-assets') }}/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->

    <script src="{{ asset('manager-assets') }}/js/off-canvas.js"></script>
    <script src="{{ asset('manager-assets') }}/js/misc.js"></script>


    @stack('scripts')

    <!-- endinject -->
    <!-- Custom js for this page -->
    <!-- End custom js for this page -->
  </body>
</html>
