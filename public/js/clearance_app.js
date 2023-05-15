const clearanceApp = new Vue({
    el: "#clearanceApp",
  
    data(){
      return{
        start: "",
        end: "",

        /*
        |--------------------------------------------------------------------------
        | Other Objects
        |--------------------------------------------------------------------------
        */
        page: 0,                      // Declaring Variable for Pagination Function
        search: "",                   // Declaring Variable for Search Function
        all_requests: [],
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
        is_get_request_sent: false,        // User data request is false initially
        is_still_request_data: true,               // User data request exist initially

      }
    },
  
    methods:{

        /*
        |--------------------------------------------------------------------------
        | Create Clearance
        |--------------------------------------------------------------------------
        */
        create_clearance(){
            if($("#create_clearance_form").valid()){

                let loader = $(".ams-loader");
                loader.css({'display':'flex','z-index':'2000'});
                
                let that  = this;

               
                let form_data = new FormData();

                form_data.append('st_session', that.start+'-'+that.end);

                axios.post('/create-clearance', form_data)
                .then(response => {
                    loader.css('display','none');
                    
                    if(response.data.status_code == 201){
                        window.location.href = base_url + "all-requests";
                    }
                    else if(response.data.status_code == 400){
                        $.map(response.data.message,function(elem,index){
                            that.exception_error(elem);
                        });
                    }
                    else if(response.data.status_code == 409){
                        that.exception_error(response.data.message);
                    }
                    else if(response.data.status_code == 401){
                        that.redirect_unauthenticated_user();
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
            that.all_requests = [];
            that.page = 0;
            that.get_all_requests();
        },

        get_all_requests(){
            this.is_get_request_sent = true;

            if (this.is_pagination_active) {
                this.page++;
            } else {
                this.page = 1;
            }

            let that = this;
            this.show_loading = true;

            axios.post('/get-all-requests', {
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
                        that.is_still_request_data = false;
                    }
                    /* Empty the array when pagination is pending
                    |------------------------------------------ */
                    if (!that.is_pagination_active) {
                        that.all_requests = [];
                    }

                    that.all_requests = that.all_requests.concat(newly_fetch_data);
                } else if (response.data.status_code == 401) {
                    that.redirect_unauthenticated_user();
                }

                that.is_pagination_active = true;
                that.is_get_request_sent = false;
            })
            .catch(function (error) {
                that.is_get_request_sent = false;
                that.show_loading = false;
                that.internal_error();
            });
        },

        /*
        |--------------------------------------------------------------------------
        | Change Request Status
        |--------------------------------------------------------------------------
        */
        change_request_status(request_data){
            /* Loading animation while request is in progress
            |---------------------------------------------- */
            let loader = $(".ams-loader");
            loader.css({'display':'flex','z-index':'2000'});

            let that = this;

            axios.post('/action-on-request', {
                request_id: request_data.id,
                status: request_data.request_status
            })
            .then(response => {
                loader.css('display','none');
                if (response.data.status_code == 200) {

                    if(response.data.data == 'Approved'){
                        request_data.request_status = true;
                    } else if(response.data.data == 'Rejected'){
                        request_data.request_status = false;
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
                    $.map(that.all_requests, function (elem, index) {
                        if (elem.id == that.delete_user_id) {
                            delete_user_index = index;
                        }
                    });
                    if (delete_user_index !== null) {
                        // Here we remove the index of deleted data from array
                        // ---------------------------------------------------
                        that.all_requests.splice(delete_user_index, 1);

                        if (that.all_requests.length <= 0) {
                            that.is_still_request_data = false;
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