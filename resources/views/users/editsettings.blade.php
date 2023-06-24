@extends('layouts.master')

@section('title', 'Edit user settings')

@section('content')
<div id="edituserApp" v-cloak>
    <div class="card shadow-lg mx-4 card-profile-bottom"></div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0"></div>
                    <form action="#" method="POST" autocomplete="off" id="edit_user_settings_form" @submit.prevent="update_user_settings()">
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
                                            <label for="password" class="form-control-label">Password</label>
                                            <input class="form-control" name="password" v-model="password" id="password" placeholder="Enter Password" type="password">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="confirm_password" class="form-control-label">Confirm Password</label>
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
                                <button class="btn btn-primary mb-0" type="submit">Update</button>
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
    <script src="{!! asset('js/edituser_app.js') !!}"></script>

    <!-- plugin for form validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

    <!-- Set Variables -->
    <script>
        edituserApp.$data.edit_user_data = <?= json_encode($edit_user_data); ?>;
        edituserApp.preview_img = edituserApp.temp_preview_img = "{{$edit_user_data->photo}}";
    </script>

    <script>
        $(document).ready(function(){
            edituserApp.populate_edit_settings_data();
        });
    </script>

    <!-- Script for form validation  -->
    <script>
        $.validator.setDefaults({ // Set ignore everything and prompt validation
            ignore: []
        });
        $().ready(function(){
            $("#edit_user_settings_form").validate({
                rules: {
                    fname: "required",
                    lname: "required",
                    password: {
                        required: false,
                        minlength: 6
                    },
                    confirm_password: {
                        required: false,
                        equalTo: "#password",
                        minlength: 6
                    },
                },
                messages: {
                    fname: "First name is required",
                    lname: "Last name is required",
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
