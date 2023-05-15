const authApp = new Vue({
  el: "#authApp",

  data(){
    return{
      name: "",
      email: "",
      type: "user",
      password: "",
      confirm_password: "",

      /*
      |--------------------------------------------------------------------------
      | Other Objects
      |--------------------------------------------------------------------------
      */
      specialization: "",
      experience: "",
      fees: "",
      edit_profile_data: [],

      /*
      |--------------------------------------------------------------------------
      | For Uploading Photo
      |--------------------------------------------------------------------------
      */
      photo: '',
      previewImage: null,

    }
  },

  methods:{

    /*
    |--------------------------------------------------------------------------
    | For Select And Preview Image
    |--------------------------------------------------------------------------
    */
    selectImage() {
      this.$refs.fileInput.click()
    },
    pickFile() {
        let input = this.$refs.fileInput
        let file = input.files
        if (file && file[0]) {
            let reader = new FileReader
            reader.onload = e => {
                this.previewImage = e.target.result
            }
            reader.readAsDataURL(file[0])
            this.$emit('input', file[0])
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Populate Edit Data Api
    |--------------------------------------------------------------------------
    */
    populate_edit_data(){
      this.name = this.edit_profile_data.name;
      this.email = this.edit_profile_data.email;
      this.specialization = this.edit_profile_data.specialization;
      this.experience = this.edit_profile_data.experience;
      this.fees = this.edit_profile_data.fees;
    },

    /*
    |--------------------------------------------------------------------------
    | Update Profile Api
    |--------------------------------------------------------------------------
    */
    update_profile(){
      if($("#edit_profile_form").valid()){

        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});
        
        let that  = this;

        /* Getting Image For Uploading
        |--------------------------- */
        let photo_input = this.$refs.fileInput;
        let photo = photo_input.files;

        const form_data = new FormData();
        
        if (photo.length) {
            form_data.append('photo', photo[0]);
        } else {
            form_data.append('photo', "");
        }

        form_data.append('name', that.name);
        form_data.append('specialization', that.specialization);
        form_data.append('experience', that.experience);
        form_data.append('fees', that.fees);
        form_data.append('password', that.password);
        form_data.append('confirm_password', that.confirm_password);

        axios.post('/update-profile-settings', form_data)
        .then(response => {
         loader.css('display','none');
          
          if(response.data.status_code == 200){
            window.location.href = base_url + "edit-profile";
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
    | Signin Api
    |--------------------------------------------------------------------------
    */
    signin(){
      if($("#signin_form").valid()){

        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});
        
        let that  = this;

        axios.post('/signin', {
          email: that.email,
          password: that.password
        })
        .then(response => {
         loader.css('display','none');
          
          if(response.data.status_code == 200){
            console.log(response.data.data)
            if(response.data.data.user_type == 'admin'){
              window.location.href = "/";
            }
          }
          else if(response.data.status_code == 402){
            that.exception_error(response.data.message);
          }
          else if(response.data.status_code == 400){
            that.exception_error(response.data.message);
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