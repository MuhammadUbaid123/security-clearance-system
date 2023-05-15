@extends('layouts.master')

@section('title', 'Create clearance')

@section('content')
<div id="clearanceApp" v-cloak>
    <div class="card shadow-lg mx-4 card-profile-bottom"></div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0"></div>
                    <form action="#" method="POST" autocomplete="off" id="create_clearance_form" @submit.prevent="create_clearance()">
                        <div class="card-body tab-content">
                            <div id="userProfileTab" class="tab-pane fade-in active">
                                <div class="d-flex justify-content-between">
                                    <p class="text-uppercase text-sm">Clearance Information</p>
                                </div>
                                <hr class="horizontal headingBottom mt-0">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="start" class="form-control-label">Start Year *</label>
                                            <input class="form-control" id="start" name="start" v-model="start" placeholder="2000">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="end" class="form-control-label">End Year *</label>
                                            <input class="form-control" id="end" name="end" v-model="end" placeholder="2000">
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
    <script src="{!! asset('js/clearance_app.js') !!}"></script>

    <!-- plugin for form validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

    <!-- Script for form validation  -->
    <script>
        $.validator.setDefaults({ // Set ignore everything and prompt validation
            ignore: []
        });
        $().ready(function(){
            $("#create_clearance_form").validate({
                rules: {
                    start: "required",
                    end: "required",

                },
                messages: {
                    start: "Start year is required",
                    end: "End year is required",
                },
            });
            
        });
    </script>
@endpush
