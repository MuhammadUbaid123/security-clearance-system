<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
    <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm text-capitalize"><a class="opacity-5 text-white" href="javascript:;">{{$parent_tab}}
        </a></li>
        <li class="breadcrumb-item text-sm text-white active text-capitalize" aria-current="page">
            <?php 
                $t_name = str_replace('_', ' ', $tab_name);
                echo $t_name;
            ?>
        </li>
        </ol>
        <h6 class="font-weight-bolder text-white mb-0 text-capitalize">
            <?php 
                $t_name = str_replace('_', ' ', $tab_name);
                echo $t_name;
            ?>
        </h6>
    </nav>

    @if(isset($session))
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
        <!-- <div class="input-group">
            <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
            <input type="text" class="form-control" placeholder="Type here...">
        </div> -->
        </div>
        <ul class="navbar-nav justify-content-center">

        <li class="nav-item d-flex align-items-center dropdown bg-transparent">
            <!-- Logout Buton Enable For (X-large, Large, Medium) Sceen -->
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Logout" class="d-lg-flex d-none align-items-center nav-link text-white font-weight-bold py-1 px-2 border border-sm-0 rounded-pill">
                <div class="me-2">
                    <img src="{{$session->photo}}" class="rounded-circle" height="50" width="50"/>
                </div>
                <div class="d-block">
                    <div class="d-md-inline text-sm d-none my-auto me-2">   
                        {{$session->fname.' '.$session->lname}}
                    </div>
                    <div class="text-xs me-2">
                        <?php 
                            $filter = str_replace('_', ' ', $session->user_type);
                            echo strtoupper($filter);
                        ?>
                    </div>
                </div>
            </a>

            <!-- Logout Buton Enable For (Small, X-small) Sceen -->
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#exampleModal" title="Logout" class="d-lg-none d-flex nav-link">
                <div>
                    <img src="{{$session->photo}}" class="rounded-circle" height="50" width="50">
                </div>
            </a>

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{route('logout')}}" method="GET">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Logout</h5>
                                <button type="button" class="ms-auto outline-none border-0 bg-transparent me-2" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="btn-close text-dark p-0" aria-hidden="true" style="font-size: 30px;">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Do you really want to logout?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn bg-gradient-primary">Yes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </li>
        <li class="nav-item d-xl-none ps-3 d-flex align-items-center" id="iconNavbarSidenav">
            <a href="javascript:;" class="nav-link text-white p-0">
            <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
                <i class="sidenav-toggler-line bg-white"></i>
            </div>
            </a>
        </li>
        @if($session->user_type != 'admin')
        <li class="nav-item px-3 d-flex align-items-center">
            <a href="" class="nav-link text-white p-0" title="Profile Settings">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
            </a>
        </li>
        @endif
        </ul>
    </div>
    @endif
    </div>
</nav>