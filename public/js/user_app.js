const userApp = new Vue({
    el: "#userApp",
  
    data(){
      return{

        fname: "",
        lname: "",
        user_type: "",
        email: "",
        department: "",
        designation: "",
        user_address: "",
        state: "",
        user_city: "",
        postal_code: "",
        password: "",
        confirm_password: "",
        
        /*
        |--------------------------------------------------------------------------
        | Other Objects
        |--------------------------------------------------------------------------
        */
        page: 0,                      // Declaring Variable for Pagination Function
        search: "",                   // Declaring Variable for Search Function
        all_users: [],
        delete_user_id: "",           // Declaring Variable for Deleting Blog

        user_information: "",

        /*
        |--------------------------------------------------------------------------
        | Flags
        |--------------------------------------------------------------------------
        */
        show_loading: false,         // Making loading false initially

        // Flags For User Module
        is_pagination_active: true,             // Initially pagination is active
        is_get_user_request_sent: false,        // User data request is false initially
        is_still_user_data: true,               // User data request exist initially

        /*
        |--------------------------------------------------------------------------
        | For Image
        |--------------------------------------------------------------------------
        */
        preview_img:"",
        temp_preview_img:"",
        photo:"",

      }
    },
  
    methods:{

        /*
        |--------------------------------------------------------------------------
        | Select image to upload 
        |--------------------------------------------------------------------------
        */
        selectImage () {
            this.$refs.fileInput.click()
        },

        /* Pick the Image file to preview before uploading to database */
        pickFile () {
            let input = this.$refs.fileInput
            let file = input.files
            if (file && file[0]) {
                let reader = new FileReader
                reader.onload = e => {
                    this.preview_img = e.target.result
                }
                reader.readAsDataURL(file[0])
                this.$emit('input', file[0])
            }
            else{
                this.preview_img = this.temp_preview_img;
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */
        create_user(){
            /* Display Error If Phone Number Is not Valid */
            $("#phone_number-error").removeClass('d-none');

            if($("#create_user_form").valid()){
                /* Display Error If Phone Number Is Valid */
                $("#phone_number-error").addClass('d-none');

                let loader = $(".ams-loader");
                loader.css({'display':'flex','z-index':'2000'});
                
                let that  = this;

                /* Getting Image For Uploading
                |--------------------------- */
                let photo_input = this.$refs.fileInput;
                let photo = photo_input.files;

                let form_data = new FormData();

                if (photo.length) {
                    form_data.append('photo', photo[0]);
                } else {
                    form_data.append('photo', "");
                }

                /* Extracting Values For Phone Number */
                var phone_iso2 = $("#phone_number").intlTelInput("getSelectedCountryData").iso2;
                var phone_dial_code = $("#phone_number").intlTelInput("getSelectedCountryData").dialCode;
                var phone_number = $("#phone_number").val();

                /* date of birth */
                var dob = $("#dob").val();

                form_data.append('fname', that.fname);
                form_data.append('lname', that.lname);
                form_data.append('user_type', that.user_type);
                form_data.append('email', that.email);
                form_data.append('department', that.department);
                form_data.append('phone_iso2', phone_iso2);
                form_data.append('phone_dial_code', phone_dial_code);
                form_data.append('phone_number', phone_number);
                form_data.append('dob', dob);
                form_data.append('designation', that.designation);
                form_data.append('state', that.state);
                form_data.append('user_city', that.user_city);
                form_data.append('user_address', that.user_address);
                form_data.append('postal_code', that.postal_code); 
                form_data.append('password', that.password);

                axios.post('/create-user', form_data)
                .then(response => {
                    loader.css('display','none');
                    
                    if(response.data.status_code == 201){
                        window.location.href = base_url + "all-users";
                    }
                    else if(response.data.status_code == 400){
                        $.map(response.data.message,function(elem,index){
                            that.exception_error(elem);
                        });
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
        | Get All Blogs
        |--------------------------------------------------------------------------
        */
        set_filter(){
            let that = this;
            that.all_users = [];
            that.page = 0;
            that.get_all_users();
        },

        get_all_users(){
            this.is_get_user_request_sent = true;

            if (this.is_pagination_active) {
                this.page++;
            } else {
                this.page = 1;
            }

            let that = this;
            this.show_loading = true;

            axios.post('/get-all-users', {
                page: that.page,
                search: that.search
            })
            .then(response => {

                that.show_loading = false;
                console.log(response);
                if (response.data.status_code == 200) {
                    let newly_fetch_data = response.data.data;
                    /* If no posts are coming then we don't need to send request again and need to show All is seen
                    |-------------------------------------------------------------------------------------------- */
                    if (newly_fetch_data.length <= 0) {
                        that.is_still_user_data = false;
                    }
                    /* Empty the array when pagination is pending
                    |------------------------------------------ */
                    if (!that.is_pagination_active) {
                        that.all_users = [];
                    }

                    that.all_users = that.all_users.concat(newly_fetch_data);
                } else if (response.data.status_code == 401) {
                    that.redirect_unauthenticated_user();
                }

                that.is_pagination_active = true;
                that.is_get_user_request_sent = false;
            })
            .catch(function (error) {
                that.is_get_user_request_sent = false;
                that.show_loading = false;
                that.internal_error();
            });
        },


        /*
        |--------------------------------------------------------------------------
        | View User
        |--------------------------------------------------------------------------
        */
        //  view_user(user_data){
        //     this.user_information = user_data;
        //     $("#view_user_modal").modal('show');
        //  },

        /*
        |--------------------------------------------------------------------------
        | Change User Status
        |--------------------------------------------------------------------------
        */
        change_user_status(user_data){
            /* Loading animation while request is in progress
            |---------------------------------------------- */
            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});

            let that = this;

            axios.post('/change-user-status', {
                id: user_data.id
            })
            .then(response => {
                loader.css('display','none');
                if (response.data.status_code == 200) {

                    if(response.data.data == 'Approved'){
                        user_data.status = true;
                    } else if(response.data.data == 'Not-Approved'){
                        user_data.status = false;
                    }

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
        | Edit User Url
        |--------------------------------------------------------------------------
        */
        set_edit_user_url(url, id){
            return url +"/"+ id;
        },

        /*
        |--------------------------------------------------------------------------
        | Delete User
        |--------------------------------------------------------------------------
        */
        set_delete_user_obj(user_data) {
            this.delete_user_id = user_data.id;
            $("#modal-delete-user").modal('show');
        },

        delete_user() {
            /* Loading animation while request is in progress
            |---------------------------------------------- */
            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});

            let that = this;
            axios.post('/delete-user', {
                id: that.delete_user_id,
            })
            .then(response => {
                loader.css('display','none');
                $("#modal-delete-user").modal('hide');
                if (response.data.status_code == 200) {

                    // To note down deleting Blog index
                    // ----------------------------------
                    let delete_user_index = null;
                    $.map(that.all_users, function (elem, index) {
                        if (elem.id == that.delete_user_id) {
                            delete_user_index = index;
                        }
                    });
                    if (delete_user_index !== null) {
                        // Here we remove the index of deleted data from array
                        // ---------------------------------------------------
                        that.all_users.splice(delete_user_index, 1);

                        if (that.all_users.length <= 0) {
                            that.is_still_user_data = false;
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