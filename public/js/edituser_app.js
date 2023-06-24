const edituserApp = new Vue({
    el: "#edituserApp",
  
    data(){
      return{
        id: "",
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
        edit_user_data: [],

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
      | Populate Data To Edit
      |--------------------------------------------------------------------------
      */
      populate_edit_data(){
          this.id = this.edit_user_data.id;
          this.fname = this.edit_user_data.fname;
          this.lname = this.edit_user_data.lname;
          this.user_type = this.edit_user_data.user_type;
          this.email = this.edit_user_data.email;
          this.department = this.edit_user_data.department;
          this.designation = this.edit_user_data.designation;
          this.user_address = this.edit_user_data.user_address;
          this.state = this.edit_user_data.state;
          this.user_city = this.edit_user_data.user_city;
          this.postal_code = this.edit_user_data.postal_code;

          this.phone_iso2 = this.edit_user_data.phone_iso2;
          setTimeout(() => {
              $('#phone_number').intlTelInput("setCountry", this.phone_iso2);
          }, 10);

          this.phone_number = this.edit_user_data.phone_number;
          setTimeout(() => {
              $('#phone_number').intlTelInput("setNumber", this.phone_number);
          }, 10);
          
          let dob = this.edit_user_data.dob;
          $("#dob").flatpickr().destroy();
          if(dob != null){
              $("#dob").flatpickr({
                  dateFormat: "Y-m-d",
                  defaultDate: new Date(dob),
              });
              setTimeout(() => {
                $(".flatpickr-day.today").removeClass("today");
              }, 10);
          }
      },

      /*
      |--------------------------------------------------------------------------
      | Update User
      |--------------------------------------------------------------------------
      */
      update_user(){
        if($("#edit_user_form").valid()){
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

            form_data.append('id', that.id);
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

            axios.post('/update-user', form_data)
            .then(response => {
                loader.css('display','none');
                
                if(response.data.status_code == 200){
                    window.location.href = base_url + "edit-user/"+ that.id;
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
      | Populate Data To Edit Settings
      |--------------------------------------------------------------------------
      */
      populate_edit_settings_data(){
        this.id = this.edit_user_data.id;
        this.fname = this.edit_user_data.fname;
        this.lname = this.edit_user_data.lname;
      },

      /*
      |--------------------------------------------------------------------------
      | Update User Settings
      |--------------------------------------------------------------------------
      */
      update_user_settings(){
        if($("#edit_user_settings_form").valid()){
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

            form_data.append('id', that.id);
            form_data.append('fname', that.fname);
            form_data.append('lname', that.lname);
            form_data.append('password', that.password);

            axios.post('/update-user-settings', form_data)
            .then(response => {
                loader.css('display','none');
                
                if(response.data.status_code == 200){
                    window.location.href = base_url + "settings";
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