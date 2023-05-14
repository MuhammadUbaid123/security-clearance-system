const scheduleApp = new Vue({
    el: "#scheduleApp",
  
    data(){
      return{
        app_session: "",                // Current App Session

        schedule_date: "",
        start_time: "",
        end_time: "",
        schedule_duration:"",
        /*
        |--------------------------------------------------------------------------
        | Other Objects
        |--------------------------------------------------------------------------
        */
        page: 0,                          // Declaring Variable for Pagination Function
        all_schedules: [],
        delete_schedule_id: "",           // Declaring Variable for Deleting Blog


        /* For Session */
        my_sessions: [],                  // Declaring Variable For My Sessions
        all_sessions: [],                 // Declaring Variable For All Sessions
        cancel_session_id: "",
        session_data: "",

        /* Add Or View Test Between (Therapist & Patient) */
        therapist_question: "",
        therapist_file: "",  
        patient_answer: "",
        patient_file: "",
        session_status: false,            // Close Session By Checking Close Session On Test Evaluation Form
        is_therapist_doc: false,
        is_patient_doc: false,

        /*
        |--------------------------------------------------------------------------
        | Flags
        |--------------------------------------------------------------------------
        */
        show_loading: false,         // Making loading false initially

        // Flags For User Module
        is_pagination_active: true,                 // Initially pagination is active
        is_get_schedule_request_sent: false,        // User data request is false initially
        is_still_schedule_data: true,               // User data request exist initially

        /* For Sessions */
        is_get_session_request_sent: false,        // User data request is false initially
        is_still_session_data: true,               // User data request exist initially

      }
    },
  
    methods:{


      /*======================================================= (Session) ==================================================== */
      /*
      |--------------------------------------------------------------------------
      | Get My Sessions
      |--------------------------------------------------------------------------
      */
      get_my_sessions(){
        this.is_get_session_request_sent = true;
        this.page++;

        let that = this;
        that.show_loading = true;

        axios.post('/get-my-sessions', {
            page: that.page,
        })
        .then(response => {

            that.show_loading = false;

            if (response.data.status_code == 200) {
                let newly_fetch_data = response.data.data;
                /* If no posts are coming then we don't need to send request again and need to show All is seen
                |-------------------------------------------------------------------------------------------- */
                if (newly_fetch_data.length <= 0) {
                    that.is_still_session_data = false;
                }

                that.my_sessions = that.my_sessions.concat(newly_fetch_data);
            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            }

            that.is_get_session_request_sent = false;
        })
        .catch(function (error) {
            that.is_get_session_request_sent = false;
            that.show_loading = false;
            that.internal_error();
        });
      },

      /*
      |--------------------------------------------------------------------------
      | Get All Schedules
      |--------------------------------------------------------------------------
      */
      get_all_sessions(){
        this.is_get_session_request_sent = true;
        this.page++;

        let that = this;
        that.show_loading = true;

        axios.post('/get-all-sessions', {
            page: that.page,
        })
        .then(response => {

            that.show_loading = false;

            if (response.data.status_code == 200) {
                let newly_fetch_data = response.data.data;
                /* If no posts are coming then we don't need to send request again and need to show All is seen
                |-------------------------------------------------------------------------------------------- */
                if (newly_fetch_data.length <= 0) {
                    that.is_still_session_data = false;
                }

                that.all_sessions = that.all_sessions.concat(newly_fetch_data);
            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            }

            that.is_get_session_request_sent = false;
        })
        .catch(function (error) {
            that.is_get_session_request_sent = false;
            that.show_loading = false;
            that.internal_error();
        });
      },


      /*
      |--------------------------------------------------------------------------
      | Update Session Status
      |--------------------------------------------------------------------------
      */
      update_session_status(session_data, set_status){

        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;
        axios.post('/update-session-status', {
            id: session_data.id,
            session_status: set_status,
        })
        .then(response => {
            loader.css('display','none');
            if (response.data.status_code == 200) {

                session_data.session_fees =  response.data.data.session_fees;             
                session_data.session_status =  response.data.data.session_status;             

            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            } else if (response.data.status_code == 404) {
                that.exception_error(response.data.message);
            }
        })
        .catch(function (error) {
            loader.css('display','none');
            that.internal_error();
        });
      },


      /*
      |--------------------------------------------------------------------------
      | Add Test
      |--------------------------------------------------------------------------
      */
      add_test(session_data){
        this.session_data = session_data;

        let that = this;
            
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});
        
        axios.post('/get-test-data',{
            id: session_data.id
        })
        .then(response => {
            loader.css('display','none');

            if(response.data.status_code == 200){

                if(response.data.data){
                    that.session_data.id = response.data.data.session_id;
                    that.session_data.t_id = response.data.data.therapist_id;
                    that.session_data.p_id = response.data.data.patient_id;
                    that.therapist_question = response.data.data.therapist_question;
                    that.patient_answer = response.data.data.patient_answer;
                    that.therapist_file = response.data.data.therapist_file;
                    that.patient_file = response.data.data.patient_file;
                    that.is_therapist_doc = response.data.data.is_therapist_doc;
                    that.is_patient_doc = response.data.data.is_patient_doc;
                }
                else{
                    that.therapist_question = "";
                    that.patient_answer = "";
                    that.therapist_file = "";
                    that.patient_file = "";
                }

                if(that.app_session.type == 'admin'){
                    $("#therapist_question-error").remove();
                    $("#therapist_question").removeClass('error');
                }
                else if(that.app_session.type == 'user'){
                    $("#patient_answer-error").remove();
                    $("#patient_answer").removeClass('error');
                }
                $("#modal-patient-test").modal('show');
            }
            else if(response.data.status_code == 404){
                $("#modal-patient-test").modal('show');
            }else if(response.data.status_code == 401){
                that.redirect_unauthenticated_user();
            }
        })
        .catch(function(error){
            loader.css('display','none');
            that.internal_error();
        });
      },
      therapistFile(event){
        this.therapist_file = event.target.files[0];
      },
      patientFile(event){
        this.patient_file = event.target.files[0];
      },

      create_test(){
        if($("#create_test_form").valid()){
            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});
            
            let that  = this;

            let form_data = new FormData();

            form_data.append('session_id', that.session_data.id);
            form_data.append('therapist_id', that.session_data.t_id);
            form_data.append('therapist_question', that.therapist_question);
            form_data.append('patient_id', that.session_data.p_id);
            form_data.append('patient_answer', that.patient_answer);
            form_data.append('session_status', that.session_status?'completed':'booked');
            
            if(that.therapist_file&&that.is_therapist_doc == false){
                form_data.append('therapist_file', that.therapist_file);
            }
            if(that.patient_file&&that.is_patient_doc == false){
                form_data.append('patient_file', that.patient_file);
            }
           
            axios.post('/create-test', form_data)
            .then(response => {
                loader.css('display','none');
                if(response.data.status_code == 201 || response.data.status_code == 200){
                    if(that.session_status == true){
                        that.session_data.session_status = 'completed';
                    }
                    $("#modal-patient-test").modal('hide');
                    that.exception_success(response.data.message);
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
      | Open Zoom Session
      |--------------------------------------------------------------------------
      */
      open_zoom_session(){
        /* Open Zoom Session In New Window */
        var newsurl = "https://pwa.zoom.us/wc/";

        let w = '900';
        let h = '500';

        /* Setting PopUp Window Position
        | ----------------------------- */  
        var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
        var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

        width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;

        var newWindow = window.open(newsurl, 'popup', 'width = ' + w + ', height = ' + h + ', left =  ' + left + ', top = ' + top);

        /* Puts focus on the newWindow
        | --------------------------- */  
        if (window.focus) {
            newWindow.focus();
        }
      },



      /*======================================================= (Schedule) ==================================================== */
      /*
      |--------------------------------------------------------------------------
      | Create Schedule
      |--------------------------------------------------------------------------
      */
      create_schedule(){
        if($("#create_schedule_form").valid()){
            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});
            
            let that  = this;

            axios.post('/create-schedule', {
                schedule_date: that.schedule_date,
                start_time: that.start_time,
                end_time: that.end_time,
                schedule_duration: that.schedule_duration,
                
            })
            .then(response => {
                loader.css('display','none');
                
                if(response.data.status_code == 201){
                    window.location.href = base_url + "all-schedules";
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
      | Get All Schedules
      |--------------------------------------------------------------------------
      */
     get_all_schedules(){
        this.is_get_schedule_request_sent = true;
        this.page++;

        let that = this;
        this.show_loading = true;

        axios.post('/get-all-schedules', {
            page: that.page,
        })
        .then(response => {

            that.show_loading = false;

            if (response.data.status_code == 200) {
                let newly_fetch_data = response.data.data;
                /* If no posts are coming then we don't need to send request again and need to show All is seen
                |-------------------------------------------------------------------------------------------- */
                if (newly_fetch_data.length <= 0) {
                    that.is_still_schedule_data = false;
                }

                that.all_schedules = that.all_schedules.concat(newly_fetch_data);
            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            }

            that.is_get_schedule_request_sent = false;
        })
        .catch(function (error) {
            that.is_get_schedule_request_sent = false;
            that.show_loading = false;
            that.internal_error();
        });
     },

    /*
    |--------------------------------------------------------------------------
    | Delete Schedule
    |--------------------------------------------------------------------------
    */
    set_delete_schedule_obj(blog_data) {
        this.delete_schedule_id = blog_data.id;
        $("#modal-delete-schedule").modal('show');
    },

    delete_schedule() {
        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;
        axios.post('/delete-schedule', {
            id: that.delete_schedule_id,
        })
        .then(response => {
            loader.css('display','none');
            $("#modal-delete-schedule").modal('hide');
            if (response.data.status_code == 200) {

                // To note down deleting Blog index
                // ----------------------------------
                let delete_schedule_index = null;
                $.map(that.all_schedules, function (elem, index) {
                    if (elem.id == that.delete_schedule_id) {
                        delete_schedule_index = index;
                    }
                });
                if (delete_schedule_index !== null) {
                    // Here we remove the index of deleted data from array
                    // ---------------------------------------------------
                    that.all_schedules.splice(delete_schedule_index, 1);
                    
                    if (that.all_schedules.length <= 0) {
                        that.is_still_schedule_data = false;
                    }
                }
                

            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            } else if (response.data.status_code == 404) {
                that.exception_error(response.data.message);
            }
        })
        .catch(function (error) {
            loader.css('display','none');
            that.internal_error();
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Update Schedule Status
    |--------------------------------------------------------------------------
    */
    update_schedule_status(schedule_data){
        /* Loading animation while request is in progress
        |---------------------------------------------- */
        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});

        let that = this;
        axios.post('/update-schedule-status', {
            id: schedule_data.id,
        })
        .then(response => {
            loader.css('display','none');
            if (response.data.status_code == 200) {
             
                schedule_data.status =  response.data.data.status;             

            } else if (response.data.status_code == 401) {
                that.redirect_unauthenticated_user();
            } else if (response.data.status_code == 404) {
                that.exception_error(response.data.message);
            }
        })
        .catch(function (error) {
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