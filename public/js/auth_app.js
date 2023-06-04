const authApp = new Vue({
  el: "#authApp",

  data(){
    return{
      fname: "",
      lname: "",
      user_type: "",
      designation: "",
      cnic: "",
      email: "",
      password: "",
      confirm_password: "",

      designation_list: [
        'Controller of exam',
        'Accounts office',
        'Treasurer',
        'Hitec university hotel',
        'Cafeteria-1',
        'Cafeteria-2',
        'Manager IT',
        'Library',
        'Margalla',
        'DSA',
        'Dean QA&C',
        'Manager Admin',
        'Student\'s supervisor',
      ],

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
    | Signup Api
    |--------------------------------------------------------------------------
    */
    signup(){
      if($("#signupform").valid()){

        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});
        
        let that  = this;

        axios.post('/signup', {
          fname: that.fname,
          lname: that.lname,
          user_type: that.user_type,
          designation: that.designation,
          cnic: that.cnic,
          email: that.email,
          password: that.password
        })
        .then(response => {
         loader.css('display','none');
          if(response.data.status_code == 200){
            window.location.href = '/signin';
          } else if(response.data.status_code == 400 || response.data.status_code == 501){
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
    | Signin Api
    |--------------------------------------------------------------------------
    */
    signin(){
      if($("#signin_form").valid()){

        let loader = $(".ams-loader");
        loader.css({'display':'flex','z-index':'2000'});
        
        let that  = this;

        axios.post('/signin', {
          user_type: that.user_type,
          cnic: that.cnic,
          email: that.email,
          password: that.password
        })
        .then(response => {
         loader.css('display','none');
          
          if(response.data.status_code == 200){
            console.log(response.data.data)
            if(response.data.data.user_type !== 'student' && response.data.data.user_type !== 'staff'){
              window.location.href = "/";
            } else if(response.data.data.user_type == 'student' || response.data.data.user_type == 'staff'){
              window.location.href = "/create-request";
            }
          }
          else if(response.data.status_code == 400 || response.data.status_code == 401 || response.data.status_code == 402 || response.data.status_code == 404){
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