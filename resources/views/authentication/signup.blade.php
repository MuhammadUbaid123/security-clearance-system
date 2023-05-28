<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Sign up
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{URL::to('/')}}/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="{{URL::to('/')}}/assets/css/nucleo-svg.css" rel="stylesheet" />
  
  <link href="{{URL::to('/')}}/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{URL::to('/')}}/assets/css/argon-dashboard.css?v=2.0.2" rel="stylesheet" />

  <link href="{{URL::to('/')}}/css/main_style.css" rel="stylesheet" />
  <link href="{{URL::to('/')}}/css/login_app.css" rel="stylesheet" />
</head>

<body class="" >
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-100" id="authApp" v-cloak>
        <div class="page-header align-items-start min-vh-50 pt-5 pb-11 m-3 border-radius-lg" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signup-cover.jpg'); background-position: top;">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container">
            <div class="row justify-content-center">
            <div class="col-lg-5 text-center mx-auto">
                <h1 class="text-white mb-2 mt-5">Welcome!</h1>
                <p class="text-lead text-white">Use these awesome forms to login or create new account in your project for free.</p>
            </div>
            </div>
        </div>
        </div>
        <div class="container">
        <div class="row mt-lg-n10 mt-md-n11 mt-n10 justify-content-center">
            <div class="col-xl-10 col-lg-8 col-md-12 mx-auto">
            <div class="card z-index-0 card-profile-bottom">
                <div class="card-header text-center pt-4">
                <h5>Registeration form</h5>
                </div>
               
                <div class="card-body">
                    <form role="form" class="row">
                        <div class="mb-3 col-6">
                            <input type="text" class="form-control" placeholder="First name" aria-label="FName">
                        </div>
                        <div class="mb-3 col-6">
                            <input type="text" class="form-control" placeholder="Last name" aria-label="LName">
                        </div>
                        <div class="mb-3 col-6">
                            <input type="text" class="form-control" placeholder="CNIC" aria-label="CNIC">
                        </div>
                        <div class="mb-3 col-6">
                            <input type="email" class="form-control" placeholder="Email" aria-label="Email">
                        </div>
                        <div class="mb-3 col-6">
                            <input type="password" class="form-control" placeholder="Password" aria-label="Password">
                        </div>
                        <div class="mb-3 col-6">
                            <input type="password" class="form-control" placeholder="Confirm password" aria-label="ConfirPassword">
                        </div>
                        <div class="form-check form-check-info text-start">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
                        <label class="form-check-label" for="flexCheckDefault">
                            I agree the <a href="javascript:;" class="text-dark font-weight-bolder">Terms and Conditions</a>
                        </label>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign up</button>
                        </div>
                        <p class="text-sm mt-3 mb-0">Already have an account? <a href="javascript:;" class="text-dark font-weight-bolder">Sign in</a></p>
                    </form>
                </div>
            </div>
            </div>
        </div>
        </div>
      </div>
    </section>
  </main>



   <!-- Success Modal -->
   <div class="modal fade" id="modal-success" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="modal-title-notification">
            Success
          </h6>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
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
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
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
  <!-- Include Vuejs & Axios Files -->
  <script src="{{URL::to('/')}}/assets/vuejs/vue2.js"></script>
  <script src="{{URL::to('/')}}/assets/axios/axios.min.js"></script>

  <!-- Include Vue Js -->
  <script src="{!! asset('js/auth_app.js') !!}"></script>

  <!--   Core JS Files   -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/core/popper.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/core/bootstrap.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="{{URL::to('/')}}/assets/js/plugins/smooth-scrollbar.min.js"></script>

  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    
   <!-- Access Base Url From ENV File -->
   <script>
    var base_url = "<?= getenv('Base_URL') ?>";
  </script>

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

  <!-- plugin for form validation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

  <!-- Script for form validation  -->
  <script>
      $(document).ready(function(){
          $("#signin_form").validate({
              rules: {
                  email: {
                      required: true,
                      email: true
                  },
                  password: { 
                      required: true, 
                      minlength: 6
                  },
              },
              messages: {
                  email: "Email is required",
                  password: {
                      required: "Password is required",
                      minlength: "At least {0} characters required!"
                  },
              },
          }); 
      });
  </script>
</html>