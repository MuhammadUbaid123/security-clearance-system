@extends('layouts.master')

@section('title', 'Create user')

@section('content')
<div id="userApp" v-cloak>
    <div class="card shadow-lg mx-4 card-profile-bottom"></div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0"></div>
                    <form action="#" method="POST" autocomplete="off" id="create_user_form" @submit.prevent="create_user()">
                        <div class="card-body tab-content">
                            <div id="userProfileTab" class="tab-pane fade-in active">
                                <div class="d-flex justify-content-between">
                                    <p class="text-uppercase text-sm">User Information</p>
                                </div>
                                <hr class="horizontal headingBottom mt-0">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="fname" class="form-control-label">First Name *</label>
                                            <input class="form-control" name="fname" v-model="fname" type="text" placeholder="Enter First Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="lname" class="form-control-label">Last Name *</label>
                                            <input class="form-control" name="lname" v-model="lname" type="text" placeholder="Enter Last Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                        <label for="user_type" class="form-control-label">User Type *</label>
                                        <select name="user_type" v-model="user_type" id="user_type" class="form-control">
                                            <option value="" selected>Select Type</option>
                                            <option value="Controller of exam">Controller of exam</option>
                                            <option value="Accounts office">Accounts office</option>
                                            <option value="Treasurer">Treasurer</option>
                                            <option value="Hitec university hotel">Hitec university hotel</option>
                                            <option value="Cafeteria-1">Cafeteria-1</option>
                                            <option value="Cafeteria-2">Cafeteria-2</option>
                                            <option value="Manager IT">Manager IT</option>
                                            <option value="Library">Library</option>
                                            <option value="Margalla">Margalla</option>
                                            <option value="DSA">DSA</option>
                                            <option value="Dean QA&C">Dean QA&C</option>
                                            <option value="Manager Admin">Manager Admin</option>
                                            <option value="Student's supervisor">Student's supervisor</option>
                                            <option value="student">Student</option>
                                            <option value="staff">Staff</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label">Email *</label>
                                            <input class="form-control" name="email" v-model="email" id="email" type="email" placeholder="Enter Email">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="department" class="form-control-label">Department</label>
                                            <input class="form-control" name="department" v-model="department" id="department" type="department" placeholder="Enter Department">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="phone_number" class="form-control-label">Phone *</label>
                                            <input class="form-control" name="phone_number" id="phone_number" type="phone" placeholder="Enter Phone Number">
                                        </div>
                                    </div>     
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="dob" class="form-control-label">DOB *</label>
                                            <input class="form-control" id="dob" name="dob" placeholder="2000-12-02">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="designation" class="form-control-label">Designation</label>
                                            <input class="form-control" name="designation" v-model="designation" id="designation" type="designation" placeholder="Enter Designation">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="state" class="form-control-label">State</label>
                                            <input class="form-control" name="state" v-model="state" placeholder="Enter State" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="user_city" class="form-control-label">User City</label>
                                            <input class="form-control" name="user_city" v-model="user_city" placeholder="Enter User City" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="postal_code" class="form-control-label">Postal Code</label>
                                            <input class="form-control" name="postal_code" v-model="postal_code" placeholder="Enter Postal Code" type="number">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                        <label for="user_address" class="form-control-label">Address</label>
                                            <input class="form-control" name="user_address" v-model="user_address" placeholder="Enter User Address" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="password" class="form-control-label">Password *</label>
                                            <input class="form-control" name="password" v-model="password" id="password" placeholder="Enter Password" type="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="confirm_password" class="form-control-label">Confirm Password *</label>
                                            <input class="form-control" name="confirm_password" v-model="confirm_password" placeholder="Enter Confirm Password" type="password">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group col-12 my_image">
                                            <div class="d-block">
                                                <div>
                                                    <label class="form-label"> Select Image</label>
                                                </div>
                                                <div>
                                                    <img alt="Image" :src="preview_img" @click.prevent="selectImage" class="pointer" width="150" height="150" title="Click here to select image">
                                                    <input ref="fileInput" type="file" @input="pickFile" accept="image/*" style="display: none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <div class="text-end my-2">
                                <button class="btn btn-primary mb-0" type="submit">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Include Vuejs -->
    <script src="{!! asset('js/user_app.js') !!}"></script>

    <!-- plugin for form validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

     <!-- Access And Set Preview Image Form Vue Js -->
    <script>
        userApp.preview_img = userApp.temp_preview_img = "{{URL::to('/')}}/assets/img/default.jpg";
    </script>
 
    <!-- plugin for phone number -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.5/js/intlTelInput.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/12.1.5/js/utils.js"></script>
    
    <!-- jquery plugin for phone number -->
    <script>
        $(function () {
            var telInput = $("#phone_number");
            var code = "+92";
            telInput.val(code);
            telInput.intlTelInput({
                autoHideDialCode: true,
                autoPlaceholder: "ON",
                dropdownContainer: document.body,
                formatOnDisplay: true,
                hiddenInput: "full_number",
                initialCountry: "auto",
                nationalMode: true,
                placeholderNumberType: "MOBILE",
                preferredCountries: ['PK','IN','CN','IR','AF'],
                separateDialCode: true
            });
        });

        /* Appear Phone Validation Error When Not Fill */
        $("#phone_number").on('keyup', function () {
            let phone_value = $("#phone_number").val();
            if (phone_value == '') {
                $("#phone_number-error").removeClass('d-none');
                $("#phone_number").addClass('error');
            } else {
                $("#phone_number-error").addClass('d-none');
                $("#phone_number").removeClass('error');
            }
        });
    </script>

    <!-- Initialize Flat Picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- script for date picker -->
    <script>
        $(document).ready(function(){
            $("#dob").flatpickr({
                dateFormat: "Y-m-d",
                onChange: function () {
                    $(".flatpickr-day.today").removeClass("today");
                }
            });
        });
    </script>

    <!-- Script for form validation  -->
    <script>
        $.validator.setDefaults({ // Set ignore everything and prompt validation
            ignore: []
        });
        $().ready(function(){
            $("#create_user_form").validate({
                rules: {
                    fname: "required",
                    lname: "required",
                    user_type: "required",
                    email: {
                        required: true,
                        email: true
                    },
                    phone_number: "required",
                    dob: "required",
                    password: {
                        required: true,
                        minlength: 6
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password",
                        minlength: 6
                    },
                },
                messages: {
                    fname: "First name is required",
                    lname: "Last name is required",
                    user_type: "User type is required",
                    email: "Email is required",
                    phone_number: "Phone number is required",
                    dob: "DOB is required",
                    password: {
                        required: "Password is required",
                        minlength: "At least {0} characters required!"
                    },
                    confirm_password: {
                        required: "Confirm password is required",
                        minlength: "At least {0} characters required!"
                    },
                },
            });
            
        });
    </script>
@endpush
