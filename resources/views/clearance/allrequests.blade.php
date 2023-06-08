@extends('layouts.master')

@section('title', 'All requests')

@section('content')
<div id="clearanceApp" v-cloak>
    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card p-4">

            <div class="d-flex p-3">
                <div class="text-secondary">
                    Showing
                    <div class="d-inline-block">
                        @{{all_requests.length}}
                    </div>
                    rows
                </div>
             
                <div class="ms-auto text-secondary">
                Search:
                    <div class="ms-2 d-inline-block text-muted">
                        <input type="text" name="search" v-model="search" id="search_request" class="form-control form-control-sm">
                    </div>
                </div>
       
            </div>

            <div class="table-responsive" >
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">ID</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Author</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Type</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Designation</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Session</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(request_data,index) in all_requests" :key="index" >
                            <td class="ps-4">
                                <span class="text-secondary text-sm font-weight-bold">@{{index+1}}</span>
                            </td>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="position-relative">
                                        <img :src="request_data.photo" class="avatar avatar-lg me-3 w-100" height="50">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <div class="d-flex">
                                            <div class="text-dark fw-bold">
                                                @{{request_data.fname+' '+request_data.lname}}
                                            </div>
                                        </div>  
                                        <p class="text-sm text-secondary mb-0">
                                            <span v-if="request_data.user_type == 'student'" class="email"> 
                                                <a :href="'mailto:'+request_data.email" class="text-secondary">@{{request_data.email}}</a>
                                            </span>
                                            <span v-else class="cnic"> 
                                                <a href="javascript:void(0);" class="text-secondary">@{{request_data.cnic}}</a>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-left text-sm">
                                <p v-if="request_data.user_type == 'student'" class="text-sm text-secondary font-weight-bold mb-0" style="white-space: normal;">
                                    Student
                                </p>
                                <p v-if="request_data.user_type == 'staff'" class="text-sm text-secondary font-weight-bold mb-0" style="white-space: normal;">
                                    Staff
                                </p>
                            </td>
                            <td class="text-left text-sm">
                                <p class="text-sm text-secondary font-weight-bold mb-0" style="white-space: normal;">
                                     @{{request_data.designation?request_data.designation: 'Graduated'}}
                                </p>
                            </td>
                           
                            <td class="text-left text-sm">
                                <p class="text-sm text-secondary font-weight-bold mb-0" style="white-space: normal;">
                                    @{{request_data.session}}
                                </p>
                            </td>

                            <td class="text-left">
                                <p class="text-sm text-secondary font-weight-bold mb-0">
                                    <span class="badge bg-warning m-1" v-if="request_data.request_status == 'pending'"> 
                                        Pending 
                                    </span>
                                    <span class="badge bg-success m-1" v-else-if="request_data.request_status == 'approved'"> 
                                        Approved
                                    </span>
                                    <span class="badge bg-danger m-1" v-else-if="request_data.request_status == 'rejected'"> 
                                        Rejected
                                    </span>
                                </p>
                               @if($session->user_type !== 'student' && $session->user_type !== 'staff')
                                    <select class="form-control form-control-sm" name="request_status" :disabled="request_data.request_status !== 'pending' ? true : false" v-model="request_data.request_status" @change.prevent="change_request_status(request_data)">
                                        <option value="pending" disabled>Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                @endif
                            </td>
                          
                      
                        </tr>
                    </tbody>
                </table>

                <div class="text-center my-3">

                    <!-- Display no found message when array is empty -->
                    <div v-if="is_still_request_data == false && all_requests.length <= 0 && !show_loading">
                        <h5 class='my-4 text-center text-secondary'>No request found</h5>
                    </div>
                    <div v-if="show_loading" class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>


                </div>

            </div>
        </div>

        <!-- Modal for Rejected User -->
        <div class="modal fade" id="modal-rejected-user" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="javascript:void(0);" method="POST" @submit.prevent="reject_request('rejecteds')">
                        <div class="modal-header">
                            <h6 class="modal-title" id="modal-title-notification">
                                Reject Request
                            </h6>
                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label class="form-label">Comments *</label>
                                    <textarea class="form-control" name="comments" v-model="comments" rows="3" required placeholder="Write Comments..."></textarea>
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Miscellaneous *</label>
                                    <textarea class="form-control" name="miscellaneous" v-model="miscellaneous" rows="4" required placeholder="Write Miscellaneous..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white text-secondary ml-auto" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-white text-danger ml-auto">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

         <!-- View User Information Modal -->
         <!-- <div class="modal fade" id="view_user_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">User Detail</h5>
                    <button type="button" class="close btn-close text-dark" style="line-height: 10px !important;" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: x-large;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="text-muted">@{{user_information.title}}</h4>
                    <p class="text-justify">
                        @{{user_information.description}}
                    </p>
                    <div>
                        <img :src="user_information.blog_photo"  class="w-100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div> -->


    </div>
</div>

@endsection


@push('scripts')
 <!-- Include Vuejs -->
    <script src="{!! asset('js/clearance_app.js') !!}"></script>

    <script>
        $(document).ready(function(){
            clearanceApp.get_all_requests();
        });
    </script>

    <!-- Search Functionality -->
    <script>
       function search_request() {
            clearanceApp.$data.is_still_request_data = true;
            clearanceApp.$data.is_pagination_active = false;
            clearanceApp.get_all_requests();
        }
        $(document).ready(function(){
            $('#search_request').keyup(function(){
                clearTimeout($.data(this, 'timer'));
                var wait = setTimeout(search_request, 500);
                $(this).data('timer', wait);
            });
        });
    </script>

    <!-- On scroll display more All users -->
    <script>
        var lastScrollTop = 0;
        $(window).on('scroll', function(event){
            var st = $(this).scrollTop();
            if (st > lastScrollTop){ // On scroll down
                if(!clearanceApp.$data.is_get_request_sent && clearanceApp.$data.is_still_request_data){
                    /* Call fetch more user function from the vue js */
                    clearanceApp.get_all_requests();
                }
            } 
            lastScrollTop = st;
        });
    </script>
@endpush
