
<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"  id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="{{url('/')}}">
        <img src="{{URL::to('/')}}/assets/img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold">Security Clearance</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    @if(isset($session))
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">

        <!-- Users (Begin) -->
        @if($session->user_type == 'admin')
        <li class="nav-item user">
          <a class="nav-link user-sect <?=$tab_name=='create_user'|| $tab_name=='edit_user' || $tab_name=='all_users'?'':'collapsed'?>" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="<?=$tab_name=='create_user'|| $tab_name=='edit_user' || $tab_name=='all_users'?'true':'false'?>" aria-controls="collapseExample">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa fa-user text-dark opacity-10"></i>
            </div>
            <span class="nav-link-text ms-1">Users</span>
          </a>
        </li>
        
        <li class="nav-item collapse ps-5 <?=$tab_name=='create_user'|| $tab_name=='edit_user' || $tab_name=='all_users'?'show':''?>" id="collapseExample">
          <a class="nav-link user-sub-list rounded-lg <?=$tab_name=='create_user'?'active':''?>" href="{{route('createuser')}}">
            <span class="nav-link-text ms-1">Create User</span>
          </a>
        </li>
        
        <li class="nav-item collapse ps-5 <?=$tab_name=='create_user' || $tab_name=='edit_user' || $tab_name=='all_users'?'show':''?>" id="collapseExample">
          <a class="nav-link user-sub-list allUsers rounded-lg <?=$tab_name=='all_users' || $tab_name=='edit_user'?'active':''?>" href="{{route('allusers')}}">
            <span class="nav-link-text ms-1">All Users</span>
          </a>
        </li>
        @endif
        <!-- Users (End) -->
        
      </ul>
      

    </div>
    @endif
  </aside>
  