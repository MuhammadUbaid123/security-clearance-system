<?php

namespace App\Http\Controllers\Webapp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Dashboard Right After Login
    |--------------------------------------------------------------------------
    */
    public function dashboard(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            return view('dashboard')
            ->with('session', $session)
            ->with('parent_tab', 'dashboard')
            ->with('tab_name', 'dashboard');
        }
        else{
            return redirect("404");
        }
    }
}
