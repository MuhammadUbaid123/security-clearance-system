@extends('layouts.master')

@section('title', 'All users')

@section('content')
<div id="userApp" v-cloak>
    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card p-4">

            <div class="d-flex p-3">
                <div class="text-secondary">
                    Showing
                    <div class="d-inline-block">
                        @{{all_users.length}} 
                    </div>
                    rows
                </div>
             
                <div class="ms-auto text-secondary">
                Search:
                    <div class="ms-2 d-inline-block text-muted">
                        @if($session->user_type !== 'user')
                        <input type="text" name="search" v-model="search" id="search_user" class="form-control form-control-sm">
                        @else
                        <select name="search" v-model="search" class="form-control form-control-sm" @change.prevent="set_filter()">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="unapproved">Unapproved</option>
                        </select>
                        @endif
                    </div>
                </div>
       
            </div>

            <div class="table-responsive" >
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">ID</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Author</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Department</th>
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Designation</th>
                            @if($session->user_type !== 'user')
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Status</th> 
                            @endif
                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user_data,index) in all_users" :key="index" >
                            <td class="ps-4">
                                <span class="text-secondary text-sm font-weight-bold">@{{user_data.id}}</span>
                            </td>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="position-relative">
                                        <img :src="user_data.photo" class="avatar avatar-lg me-3 w-100" height="50">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <div class="d-flex">
                                            <div class="text-dark fw-bold">
                                                @{{user_data.fname+' '+user_data.lname}}
                                            </div>
                                        </div>  
                                        <p class="text-sm text-secondary mb-0">
                                            <span class="email"> 
                                                <a :href="'mailto:'+user_data.email" class="text-secondary">@{{user_data.email}}</a>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-left text-sm">
                                <p class="text-sm text-secondary font-weight-bold mb-0" style="white-space: normal;">
                                    @{{user_data?.department?user_data.department:'Not mentioned'}}
                                </p>
                            </td>
                            <td class="text-left text-sm">
                                <p class="text-sm text-secondary font-weight-bold mb-0" style="white-space: normal;">
                                    @{{user_data.designation}}
                                </p>
                            </td>
                            @if($session->user_type !== 'user')
                            <td class="text-left">
                                <p class="text-sm text-secondary font-weight-bold mb-0">
                                    <span class="badge bg-success m-1" v-if="user_data.status"> 
                                        Approved 
                                    </span>
                                    <span class="badge bg-danger m-1" v-else-if="!user_data.status"> 
                                        Not-Approved 
                                    </span>
                                </p>
                                @if($session->user_type !== 'user')
                                <div class="form-check form-switch ms-1">
                                    <input v-if="user_data.status" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" checked="" @change.prevent="change_user_status(user_data)">
                                    <input v-else-if="!user_data.status" class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" @change.prevent="change_user_status(user_data)">
                                </div>
                                @endif
                            </td>
                            @endif
                            <td>
                                <!-- <a href="javascript:void(0);" @click.prevent="view_User(user_data)" class="btn p-2 px-3 m-1" data-toggle="tooltip" data-placement="top" title="View User">
                                    <i class="fa fa-eye text-info"></i>
                               </a> -->
                                @if($session->user_type !== 'user')
                                <a :href="set_edit_user_url('<?= route('edituser') ?>', user_data.id)" class="btn p-2 px-3 m-1" data-toggle="tooltip" data-placement="top" title="Edit User">
                                    <i class="fa fa-edit text-primary"></i>
                                </a>
                                <a href="javascript:void(0);" @click.prevent="set_delete_user_obj(user_data)" class="btn p-2 px-3 m-1" data-toggle="tooltip" data-placement="top" title="Delete User">
                                    <i class="fa fa-trash text-danger"></i>
                                </a>
                                @endif
                            </td>
                          
                      
                        </tr>
                    </tbody>
                </table>

                <div class="text-center my-3">

                    <!-- Display no found message when array is empty -->
                    <div v-if="is_still_user_data == false && all_users.length <= 0 && !show_loading">
                        <h5 class='my-4 text-center text-secondary'>No user found</h5>
                    </div>
                    <div v-if="show_loading" class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>


                </div>

            </div>
        </div>

        <!-- Modal for Delete User -->
        <div class="modal fade" id="modal-delete-user" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="modal-title-notification">
                            Delete User
                        </h6>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="py-3 text-center">
                            <i class="fa fa-trash fa-3x text-danger"></i>
                            <h4 class="text-secondary mt-4">
                                Are you sure to delete this user permanently?
                            </h4>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white text-secondary ml-auto" data-bs-dismiss="modal">No</button>
                        <button type="button" @click.prevent="delete_user()" class="btn btn-white text-danger ml-auto" data-bs-dismiss="modal">Yes</button>
                    </div>
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
    <script src="{!! asset('js/user_app.js') !!}"></script>

    <script>
        $(document).ready(function(){
            userApp.get_all_users();
        });
    </script>

    <!-- Search Functionality -->
    <script>
       function search_user() {
            userApp.$data.is_still_user_data = true;
            userApp.$data.is_pagination_active = false;
            userApp.get_all_users();
        }
        $(document).ready(function(){
            $('#search_user').keyup(function(){
                clearTimeout($.data(this, 'timer'));
                var wait = setTimeout(search_user, 500);
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
                if(!userApp.$data.is_get_user_request_sent && userApp.$data.is_still_user_data){
                    /* Call fetch more user function from the vue js */
                    userApp.get_all_users();
                }
            } 
            lastScrollTop = st;
        });
    </script>
@endpush
