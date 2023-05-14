const therapistApp = new Vue({
    el: "#therapistApp",
  
    data(){
      return{
        session: "",
        /*
        |--------------------------------------------------------------------------
        | Other Objects
        |--------------------------------------------------------------------------
        */
        page: 0,                      // Declaring Variable for Pagination Function
        search: "",                   // Declaring Variable for Search Function
        all_therapists: [],



        /* For Therapist Profile With His Own Schedules */
        filter_therapist_id: "",           // Declaring Variable for Getiing user id
        filter_therapist_info: [{          // Getiing Data into This Variable
            therapist_id: "",
            photo : '',
            name :  '',
            fees: "",
            specialization :  '',
        }],
        filter_therapist_schedules: [],      // Geting Specific Therapist Schedules Data 
        
        /* For Session Booking */
        session_start_time: "",             //For Patient Selection
        session_end_time: "",               //For Patient Selection
        therapist_details: "",              // Getting Consultant Therapist Details
        email: "",                          // Email For Credentials
        password: "",                       // Password For Credentials

        /* Therapist Review And Rating */
        rating: 1,                          // Rating Stars
        reviews: "",                        // Rating Reviews
        therapist_data: "",                 // Rate Therapist Id
        review_data: [],                    // Read Therapist Reviews
        is_reviews_available: true,         // Check Condition To Enable Or Disable Reviews Preview

        /*
        |--------------------------------------------------------------------------
        | Flags
        |--------------------------------------------------------------------------
        */
        show_loading: false,         // Making loading false initially
        show_modal_loading: false,         // Making loading false initially

        // Flags For User Module
        is_pagination_active: true,             // Initially pagination is active
        is_get_therapist_request_sent: false,        // User data request is false initially
        is_still_therapist_data: true,               // User data request exist initially

        is_get_schedule_request_sent: false,         // User History request is false initially
        is_still_therapist_schedule_data: true,      // User data request exist initially

        /* For Session Booking First Ask For Credentials */
        is_credentials_valid: true

      }
    },
  
    methods:{


      /*
      |--------------------------------------------------------------------------
      | View Therapist Profile
      |--------------------------------------------------------------------------
      */
      view_therapist_profile(therapist_data){

        this.filter_therapist_id = therapist_data.id;
            
            var filter_therapist_info = [];
            let single_object = {
                therapist_id: therapist_data.id,
                photo :  therapist_data.photo,
                name :  therapist_data.name,
                fees :  therapist_data.fees,
                specialization :  therapist_data.specialization,
            };
            filter_therapist_info.push(single_object);
            this.filter_therapist_info = filter_therapist_info;
            
            let that = this;
            that.get_schedules_by_therapist();
            $("#therapist_profile_modal").modal('show');
      },

      get_schedules_by_therapist(){
        this.is_get_schedule_request_sent = true;
        
        this.page++;

        let that = this;

        that.filter_therapist_schedules = [];
        that.show_modal_loading = true;

        axios.post('/get-schedules-by-therapist', {
            id: that.filter_therapist_id,
        })
        .then(response => {

            that.show_modal_loading = false;

            if (response.data.status_code == 200) {
                let newly_fetch_data = response.data.data;
                /* If no posts are coming then we don't need to send request again and need to show All is seen
                |-------------------------------------------------------------------------------------------- */
                if (newly_fetch_data.length <= 0) {
                    that.is_still_therapist_schedule_data = false;
                }
                

                that.filter_therapist_schedules = that.filter_therapist_schedules.concat(newly_fetch_data);
            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            }

            that.is_get_schedule_request_sent = false;
        })
        .catch(function (error) {
            that.is_get_schedule_request_sent = false;
            that.show_modal_loading = false;
            that.internal_error();
        });
      },

      /*
      |--------------------------------------------------------------------------
      | Get Therapist Consultation
      |--------------------------------------------------------------------------
      */
      get_consultation(schedule_data,therapist_data){
        schedule_data['therapist_id'] = therapist_data.therapist_id;
        schedule_data['fees'] = therapist_data.fees;
        this.therapist_details = schedule_data;
        this.session_start_time = schedule_data.start_time;
        this.session_end_time = schedule_data.end_time;
        $("#make_session").modal('show');
      },
      book_session(){
        if($("#book_session").valid()){

            /* Payment Verifiaction */
            if(this.is_credentials_valid){
                $("#modal-payment-verification").modal("show");
                return;
            }

            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});
            
            let that  = this;

            axios.post('/book-session', {
                therapist_id: that.therapist_details.therapist_id,
                schedule_id: that.therapist_details.id,
                start_time: that.session_start_time,
                end_time: that.session_end_time,
                // fees: that.therapist_details.fees,
            })
            .then(response => {
                loader.css('display','none');
                
                if(response.data.status_code == 201){
                    window.location.href = base_url + "my-sessions";
                } else if(response.data.status_code == 404){
                    that.exception_error(response.data.message);
                }
                else if(response.data.status_code == 422){
                    $.map(response.data.error_details,function(elem,index){
                        that.exception_error(elem);
                    });
                }
            })
            .catch(function(error){
                loader.css('display','none');
                that.internal_error();
            });
        }
      },

      /*
      |--------------------------------------------------------------------------
      | Payment Verification
      |--------------------------------------------------------------------------
      */
      payment_verification(){
        if($("#payment_verification").valid()){

            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});
            
            let that  = this;

            axios.post('/payment-verification', {
                email: that.email,
                password: that.password,
            })
            .then(response => {
                loader.css('display','none');
                
                if(response.data.status_code == 200){
                    that.is_credentials_valid = false;
                    $("#modal-payment-verification").modal("hide");
                    that.exception_success(response.data.message);
                } else if(response.data.status_code == 401){
                    that.redirect_unauthenticated_user();
                } else if(response.data.status_code == 402 || response.data.status_code == 404){
                    that.exception_error(response.data.message);
                } else if(response.data.status_code == 422){
                    $.map(response.data.error_details,function(elem,index){
                        that.exception_error(elem);
                    });
                }
            })
            .catch(function(error){
                loader.css('display','none');
                that.internal_error();
            });
        }
      },
  
      /*
      |--------------------------------------------------------------------------
      | Get All Therapists
      |--------------------------------------------------------------------------
      */
      set_filter(){
        let that = this;
        that.all_therapists = [];
        that.page = 0;
        that.get_all_therapists();
      },

      get_all_therapists(){
        this.is_get_therapist_request_sent = true;

        if (this.is_pagination_active) {
            this.page++;
        } else {
            this.page = 1;
        }

        let that = this;
        this.show_loading = true;

        axios.post('/get-all-therapists', {
            page: that.page,
            search: that.search
        })
        .then(response => {

            that.show_loading = false;

            if (response.data.status_code == 200) {
                let newly_fetch_data = response.data.data;
                /* If no posts are coming then we don't need to send request again and need to show All is seen
                |-------------------------------------------------------------------------------------------- */
                if (newly_fetch_data.length <= 0) {
                    that.is_still_therapist_data = false;
                }
                /* Empty the array when pagination is pending
                |------------------------------------------ */
                if (!that.is_pagination_active) {
                    that.all_therapists = [];
                }

                /* Set Therapist Rating */
                $.map(newly_fetch_data,function(elem,index){
                    for(let i=1; i<=elem.rating_value.toPrecision(1); i++){
                        elem['ratenum'] = i;
                        elem['notratenum'] =  5-i;
                    }
                });

                that.all_therapists = that.all_therapists.concat(newly_fetch_data);
            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            }

            that.is_pagination_active = true;
            that.is_get_therapist_request_sent = false;
        })
        .catch(function (error) {
            that.is_get_therapist_request_sent = false;
            that.show_loading = false;
            that.internal_error();
        });
      },

      /*
      |--------------------------------------------------------------------------
      | Verify Therapist Profile
      |--------------------------------------------------------------------------
      */
      verify_therapist_profile(e, therapist_data){
        var is_checked = $(e.target).prop("checked");

        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;

        axios.post('/verify-therapist-profile', {
            id: therapist_data.id,
            status: (is_checked ? "verified" : "unverified")
        })
        .then(response => {
            loader.css('display','none');
            if (response.data.status_code == 200) {

                therapist_data.status = response.data.data.status;
                // console.log(response.data.data.status);

            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            } else if (response.data.status_code == 404) {
                that.exception_error(response.data.message);
            }
        })
        .catch(function(error) {
            loader.css('display','none');
            that.internal_error();
        });
      },

      /*
      |--------------------------------------------------------------------------
      | Rate Therapist Profile
      |--------------------------------------------------------------------------
      */
      rate_therpist(therapist_data){
        this.therapist_data = therapist_data;
        this.rating = 1;
        this.reviews = "";
        $("#modal-rate-therapist").modal('show');
      },
      save_therapist_rating(){

        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;

        axios.post('/give-rating', {
            therapist_id: that.therapist_data.id,
            rating: that.rating,
            reviews: that.reviews
        })
        .then(response => {
            loader.css('display','none');
            if (response.data.status_code == 200) {


                $("#modal-rate-therapist").modal('hide');
                that.exception_success(response.data.message);

                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 1000);

            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            } else if (response.data.status_code == 404) {
                that.exception_error(response.data.message);
            }
        })
        .catch(function(error) {
            loader.css('display','none');
            that.internal_error();
        });
      },

      /*
      |--------------------------------------------------------------------------
      | Get Reviews By Therapist
      |--------------------------------------------------------------------------
      */
     get_reviews_by_therapist(therapist_data){
        this.review_data = [];

        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;

        axios.post('/get-reviews-by-therapist', {
            id: therapist_data.id
        })
        .then(response => {
            loader.css('display','none');
            if (response.data.status_code == 200) {
                that.review_data = response.data.data;
                if(that.review_data.length>0){
                    that.is_reviews_available = true;
                }
                else{
                    that.is_reviews_available = false;
                }

                $("#modal-therapist-reviews").modal('show');
            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            }
        })
        .catch(function(error) {
            loader.css('display','none');
            that.internal_error();
        });
     },



      /*
      |--------------------------------------------------------------------------
      | Block / Unblock Therapist Profile
      |--------------------------------------------------------------------------
      */
      block_therapist_profile(e, therapist_data){
        var is_checked = $(e.target).prop("checked");

        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;

        axios.post('/block-user-profile', {
            id: therapist_data.id,
            is_blocked: (is_checked ? 1:0)
        })
        .then(response => {
            loader.css('display','none');
            if (response.data.status_code == 200) {

                therapist_data.is_blocked = response.data.data.is_blocked;

            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            } else if (response.data.status_code == 404) {
                that.exception_error(response.data.message);
            }
        })
        .catch(function(error) {
            loader.css('display','none');
            that.internal_error();
        });
      },
  

      /*
      |--------------------------------------------------------------------------
      | Convert Time Into AM/PM
      |--------------------------------------------------------------------------
      */
      timeConvert(time) {
        var hourEnd = time.indexOf(":");
        var H = +time.substr(0, hourEnd);
        var h = H % 12 || 12;
        var ampm = (H < 12 || H === 24) ? " AM" : " PM";
        return  h + time.substr(hourEnd, 3) + ampm;
      },
  
      /*
      |--------------------------------------------------------------------------
      | Alerts
      |--------------------------------------------------------------------------
      */
      internal_error(){
        $("#modal-failed").modal('show');
        $("#modal-failed .modal-body .body_text").text("There is some internal error!");
      },
  
      exception_error(message){
          $("#modal-failed").modal('show');
          $("#modal-failed .modal_title").text("Oops!");
          $("#modal-failed .modal-body .body_text").text(message);
      },

      exception_success(message){
        $("#modal-success").modal('show');
        $("#modal-success .modal_title").text("Success!");
        $("#modal-success .modal-body .body_text").text(message);
      },

      redirect_unauthenticated_user() {
        let that = this;
        axios.get('/logout')
        .then(response => {
            if (response) {
                window.location.href = "/";
            }
        })
        .catch(function(error) {
            that.internal_error();
        });
      }
  
    }
  });