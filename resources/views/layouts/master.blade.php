<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{URL::to('/')}}/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="{{URL::to('/')}}/assets/img/favicon.png">
  <title>
    @yield('title')
  </title>
  
  <!-- Fonts and icons -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  
  <!-- Nucleo Icons -->
  <link href="{{URL::to('/')}}/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="{{URL::to('/')}}/assets/css/nucleo-svg.css" rel="stylesheet" />
  <link href="{{URL::to('/')}}/assets/css/nucleo-svg.css" rel="stylesheet" />
  
  <!-- CSS Files -->
  <link id="pagestyle" href="{{URL::to('/')}}/assets/css/argon-dashboard.css?v=2.0.2" rel="stylesheet" />
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <!-- plugin for form validation -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300.css">

  <!-- css for phone number plugin  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.5/css/intlTelInput.css" />

  <!-- Date pickers -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  
  <!-- My custom css file linked here  -->
  <link href="{{URL::to('/')}}/css/main_style.css" rel="stylesheet" />
  <!-- Loader css file linked here  -->
  <link rel="stylesheet" href="{{URL::to('/')}}/css/appstyle.css">

  @stack('styles')
  
</head>
<body class="g-sidenav-show bg-gray-100">
  
      <!-- Switching Between Bg Cover According To Current Page -->
    <?php
        $uri = $_SERVER["REQUEST_URI"];
        $uriArray = explode('/', $uri);
        $url_name = $uriArray[1];
        if($url_name == '' || $url_name == 'all-users' || $url_name == 'create-user' || $url_name == 'edit-user' || 
          $url_name == 'create-clearance' || $url_name == 'all-requests'){
          $base_url = getenv('Base_URL');
          $url_image = $base_url.'assets/img/profile_layout_header.jpg';
          echo "<div class='position-absolute w-100 min-height-300 top-0' 
          style='background-image: url(".$url_image."); background-position: center;'>
            <span class='mask bg-primary opacity-6'></span>
          </div>";
        }else{
          echo '<div class="min-height-300 bg-primary position-absolute w-100"></div>';
        }
    ?>


    <!-- Include Side Nav (Begin) -->
        @include('layouts.sidenav')
    <!-- Include Side Nav (End) -->

    <main class="main-content position-relative border-radius-lg ">

      <!-- Include Top Nav (Begin) -->
          @include('layouts.topnav')
      <!-- Include Top Nav (End) -->

        <!-- Include Content (Begin) -->
          @yield('content')
        <!-- Include Content (End) -->
      
        <div class="container-fluid pb-4">
          <!-- Include footer scetion (Begin) -->
          @include('layouts.footer')
          <!-- Include footer scetion (Begin) -->
        </div>

    </main>
  


    <!-- Success Modal -->
  <div class="modal fade" id="modal-success" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="modal-title-notification">
            Success
          </h6>
          <button type="button" class="close btn-close text-dark" style="line-height: 10px !important;" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="font-size: x-large;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="py-3 text-center">
            <i class="fa fa-check-circle fa-3x text-success"></i>
            <h4 class="body_text text-secondary mt-4">
              ...
            </h4>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white text-dark ml-auto" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


  <!-- Failed Modal -->
  <div class="modal fade" id="modal-failed" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="modal-title-notification">
            Failed
          </h6>
          <button type="button" class="close btn-close text-dark" style="line-height: 10px !important;" data-bs-dismiss="modal" aria-label="Close">
              <span aria-hidden="true" style="font-size: x-large;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="py-3 text-center">
            <i class="fa fa-times-circle fa-3x text-danger"></i>
            <h4 class="body_text text-secondary mt-4">
              ...
            </h4>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-white text-dark ml-auto" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

    <!-- Loading Animation -->
    <div class="ams-loader">
        <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

</body>

  <!--   Core JS Files   -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/core/popper.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/core/bootstrap.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/plugins/chartjs.min.js"></script>

  <!-- Include Vuejs & Axios Files -->
  <script src="{{URL::to('/')}}/assets/vuejs/vue2.js"></script>
  <script src="{{URL::to('/')}}/assets/axios/axios.min.js"></script>

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js"></script>

  
  <!-- Access Base Url From ENV File -->
  <script>
    var base_url = "<?= getenv('Base_URL') ?>"; 
  </script>

  @stack('scripts')
 
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Loading Animation Script -->
  <script>
      window.addEventListener("load", function () {
        const loader = document.querySelector(".ams-loader");
        loader.className += " hidden"; // class "loader hidden"
      });
  </script>


  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="{{URL::to('/')}}/assets/js/argon-dashboard.min.js?v=2.0.2"></script>

</html>