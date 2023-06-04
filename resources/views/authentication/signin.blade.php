<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Sign In
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
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
              <div class="card card-plain">
                <div class="card-header pb-0 text-start">
                  <h4 class="font-weight-bolder">Sign In</h4>
                  <p class="mb-0">Enter your credentials to sign In</p>
                </div>
                <div class="card-body">
                  <form action="#" method="POST" @submit.prevent="signin()" id="signin_form">
                    <div class="mb-3">
                        <select class="form-control form-control-sm" name="user_type" id="user_type" v-model="user_type">
                            <option value="" selected>Select Type</option>
                            <option value="admin">Admin</option>
                            <option value="concerned_person">Concerned Person</option>
                            <option value="staff">Staff</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div class="mb-3" v-if="user_type !== 'student' && user_type !== '' && user_type !== 'admin'">
                      <input type="text" id="cnic" name="cnic" v-model="cnic" class="form-control form-control-sm" placeholder="CNIC" aria-label="cnic">
                    </div>
                    <div class="mb-3" v-if="user_type == 'student' || user_type == 'admin'">
                      <input type="email" id="email" name="email" v-model="email" class="form-control form-control-sm" :placeholder="user_type == 'admin' ? 'info@gmail.com' : '19-cs-017@student.hitecuni.edu.pk'" aria-label="Email">
                    </div>
                    <div class="mb-3">
                      <input type="password" id="password" name="password"  v-model="password" class="form-control form-control-sm" placeholder="Password" aria-label="Password">
                    </div>
                    <a href="{{route('signup')}}" class="link-primary text-sm">Create new account</a>
                    <div class="text-center">
                      <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-3 mb-0">Sign In</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
              <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden" style="background-image: url('../assets/img/hitec.jpg');
          background-size: cover  ; background-repeat:no-repeat; background-position:center;">
                <span class="mask bg-gradient-primary opacity-6"></span>
                <h4 class="mt-5 text-white font-weight-bolder position-relative">"Welcome to Hitec Clearance System"</h4>
                <p class="text-white position-relative">"Automation is the embodiment of the old adage; But, automation is much more complex than simply flipping"</p>
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