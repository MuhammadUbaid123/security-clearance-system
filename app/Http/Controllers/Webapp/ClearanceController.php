<?php

namespace App\Http\Controllers\Webapp;

use App\Http\Controllers\Controller;
use App\Models\ConsultedSession;
use App\Models\User;
use App\Traits\Get;
use App\Traits\Post;
use Illuminate\Http\Request;
use CURLFile;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\Delete;

class ClearanceController extends Controller
{
    use Post;
    use Get;
    use Delete;

    /*
    |--------------------------------------------------------------------------
    | Create Clearance
    |--------------------------------------------------------------------------
    */ 
    public function show_create_clearance(Request $request){
        $session = $request->session()->get("login_data");

        if($session && $session->user_type == 'student' || $session->user_type == 'staff'){
            return view('clearance.createclearance')
            ->with('session', $session)
            ->with('parent_tab', 'clearance')
            ->with('tab_name', 'create_clearance');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Clearance Api
    |--------------------------------------------------------------------------
    */ 
    public function create_clearance(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $url = env('Api_Base_URL')."api/create-clearance-request";
    
            $data = array(
                'st_session' => $request->st_session,
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }     
    }


    /*
    |--------------------------------------------------------------------------
    | Show All Requests
    |--------------------------------------------------------------------------
    */ 
    public function show_all_requests(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            return view('clearance.allrequests')
            ->with('session', $session)
            ->with('parent_tab', 'requests')
            ->with('tab_name', 'all_requests');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Requests Api
    |--------------------------------------------------------------------------
    */ 
    public function get_all_requests(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $count = $request->page;
            $search = urlencode($request->search);

            $url = getenv("Api_Base_URL")."api/all-requests?page=$count&search=$search";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | Change Clearance Status
    |--------------------------------------------------------------------------
    */ 
    public function change_request_status(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;

            $url = getenv("Api_Base_URL")."api/action-on-request";

            $data = array(
                'request_id' => $request->request_id,
                'status' => $request->status
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }
    }

}
