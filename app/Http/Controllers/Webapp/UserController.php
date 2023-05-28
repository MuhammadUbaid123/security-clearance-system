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

class UserController extends Controller
{
    use Post;
    use Get;
    use Delete;

    public function dashboard(Request $request)
    {
        $session = $request->session()->get("login_data");

        if($session)
        {
            return view('dashboard')
            ->with('tab_name','daashboard')
            ->with('parent_tab', 'dashboard');
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Create User
    |--------------------------------------------------------------------------
    */ 
    public function show_create_user(Request $request){
        $session = $request->session()->get("login_data");

        if($session && $session->user_type == 'super_admin' || $session->user_type == 'admin'){
            return view('users.createuser')
            ->with('session', $session)
            ->with('parent_tab', 'users')
            ->with('tab_name', 'create_user');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create User Api
    |--------------------------------------------------------------------------
    */ 
    public function create_user(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $photo = "";
            if($request->photo){
                $photo = new CURLFile($request->photo);
            }

            $url = env('Api_Base_URL')."api/create-user";
    
            $data = array(
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'department' => $request->department,
                'phone_iso2' => $request->phone_iso2,
                'phone_dial_code' => $request->phone_dial_code,
                'phone_number' => $request->phone_number,
                'dob' => $request->dob,
                'designation' => $request->designation,
                'state' => $request->state,
                'user_city' => $request->user_city,
                'postal_code' => $request->postal_code,
                'user_address' => $request->user_address,
                'password' => $request->password,
                'photo' => $photo
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }     
    }

    /*
    |--------------------------------------------------------------------------
    | Show All Users
    |--------------------------------------------------------------------------
    */ 
    public function show_all_users(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            return view('users.allusers')
            ->with('session', $session)
            ->with('parent_tab', 'users')
            ->with('tab_name', 'all_users');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Users Api
    |--------------------------------------------------------------------------
    */ 
    public function get_all_users(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $count = $request->page;
            $search = urlencode($request->search);

            $url = getenv("Api_Base_URL")."api/all-users?page=$count&search=$search";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Edit User
    |--------------------------------------------------------------------------
    */ 
    public function show_edit_user(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $id = $request->id;
            $token = $session->token;

            $url = getenv("Api_Base_URL")."api/single-user?id=$id";
            $response = $this->curlGet_token($url, $token);

            if($response){
                $edit_user_data = json_decode($response);
                if($edit_user_data->status_code == 200){
                    return view('users.edituser')
                    ->with('session', $session)
                    ->with('edit_user_data', $edit_user_data->data)
                    ->with('parent_tab', 'users')
                    ->with('tab_name', 'edit_user');
                }
                else{
                    return redirect("404");
                }
            }
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */ 
    public function update_user(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $photo = "";
            if($request->photo){
                $photo = new CURLFile($request->photo);
            }

            $url = env('Api_Base_URL')."api/update-user";
    
            $data = array(
                'id' => $request->id,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'user_type' => $request->user_type,
                'department' => $request->department,
                'phone_iso2' => $request->phone_iso2,
                'phone_dial_code' => $request->phone_dial_code,
                'phone_number' => $request->phone_number,
                'dob' => $request->dob,
                'designation' => $request->designation,
                'state' => $request->state,
                'user_city' => $request->user_city,
                'postal_code' => $request->postal_code,
                'user_address' => $request->user_address,
                'password' => $request->password,
                'photo' => $photo
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete User
    |--------------------------------------------------------------------------
    */ 
    public function delete_user(Request $request){
        $session = $request->session()->get("login_data");
        if($session){
            $id = $request->id;
            $token = $session->token;
            
            $url = env('Api_Base_URL')."api/delete-user?id=$id";

            $response = $this->curlDelete_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Change User Status
    |--------------------------------------------------------------------------
    */ 
    public function change_user_status(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;

            $url = getenv("Api_Base_URL")."api/change-user-status";

            $data = array(
                'id' => $request->id
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }
    }

}
