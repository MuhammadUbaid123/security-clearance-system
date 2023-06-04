<?php

namespace App\Http\Controllers\Webapp;

use App\Http\Controllers\Controller;
use App\Traits\Post;
use App\Traits\Get;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use Post;
    use Get;

    /*
    |--------------------------------------------------------------------------
    | Show Signup Screen
    |--------------------------------------------------------------------------
    */
    public function show_signup(Request $request){
        return view('authentication.signup')
            ->with('parent_tab', 'signup')
            ->with('tab_name', 'signup');
    }

    /*
    |--------------------------------------------------------------------------
    | Signup Api
    |--------------------------------------------------------------------------
    */
    public function signup(Request $request){
        $url = env('API_BASE_URL')."api/signup";

        $data = array(
            'fname' => $request->fname,
            'lname' => $request->lname,
            'user_type' => $request->user_type,
            'designation' => $request->designation,
            'cnic' => $request->cnic,
            'email' => $request->email,
            'password' => $request->password,
        );
            
        $response = $this->curlPost($url, $data);
        return $response;
    }

    /*
    |--------------------------------------------------------------------------
    | Show Signin Screen
    |--------------------------------------------------------------------------
    */
    public function show_signin(Request $request){
        return view('authentication.signin')
            ->with('parent_tab', 'signin')
            ->with('tab_name', 'signin');
    }

    /*
    |--------------------------------------------------------------------------
    | Signin Api Call
    |--------------------------------------------------------------------------
    */
    public function signin(Request $request){
        $url = env('API_BASE_URL')."api/login";

        $data = array(
            'user_type' => $request->user_type,
            'cnic' => $request->cnic,
            'email' => $request->email,
            'password' => $request->password,
        );
            
        $response = $this->curlPost($url, $data);

        $data = json_decode($response);
        if($data){
            if($data->status_code == 200){
                $request->session()->put('login_data', $data->data);
            }
        }
        return $response;
    }


    /*
    |--------------------------------------------------------------------------
    | Logout Api Call
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request){
        $session = $request->session()->get("login_data");
        $token = $session->token;
        if($token){
            $url = env('API_BASE_URL')."api/logout";
            $this->curlLogout($url, $token);
        }
        Session::flush();
        Session::forget('login_data');
        return redirect()->route('signin');
    }

}
