<?php

namespace App\Http\Controllers\Webapp;

use App\Http\Controllers\Controller;
use App\Traits\Delete;
use App\Traits\Get;
use App\Traits\Post;
use Illuminate\Http\Request;
use CURLFile;

class BlogController extends Controller
{
    use Post;
    use Get;
    use Delete;

    /*
    |--------------------------------------------------------------------------
    | Create Blog
    |--------------------------------------------------------------------------
    */ 
    public function show_create_blog(Request $request){
        $session = $request->session()->get("login_data");

        if($session && $session->type == 'admin'){
            return view('blogs.createblog')
            ->with('session', $session)
            ->with('parent_tab', 'blogs')
            ->with('tab_name', 'create_blog');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Blog Api
    |--------------------------------------------------------------------------
    */ 
    public function create_blog(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $photo = "";
            if($request->photo){
                $photo = new CURLFile($request->photo);
            }

            $url = env('Api_Base_URL')."api/create-blog";
    
            $data = array(
                'title' => $request->title,
                'description' => $request->description,
                'photo' => $photo
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }     
    }

    /*
    |--------------------------------------------------------------------------
    | Approve Api
    |--------------------------------------------------------------------------
    */ 
    public function approve_blog(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $id = $request->id;

            $url = getenv("Api_Base_URL")."api/approve-blog?id=$id";

            $data = array(
                'status' => $request->status
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show All Blogs
    |--------------------------------------------------------------------------
    */ 
    public function show_all_blogs(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            return view('blogs.allblogs')
            ->with('session', $session)
            ->with('parent_tab', 'blogs')
            ->with('tab_name', 'all_blogs');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Blogs Api
    |--------------------------------------------------------------------------
    */ 
    public function get_all_blogs(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $count = $request->page;
            $search = urlencode($request->search);

            $url = getenv("Api_Base_URL")."api/get-all-blogs?page=$count&search=$search";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show Edit Blog
    |--------------------------------------------------------------------------
    */ 
    public function show_edit_blog(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $id = $request->id;
            $token = $session->token;

            $url = getenv("Api_Base_URL")."api/get-single-blog-to-edit?id=$id";
            $response = $this->curlGet_token($url, $token);

            if($response){
                $edit_blog_data = json_decode($response);
                if($edit_blog_data->status_code == 200){
                    return view('blogs.editblog')
                    ->with('session', $session)
                    ->with('edit_blog_data', $edit_blog_data->data)
                    ->with('parent_tab', 'blogs')
                    ->with('tab_name', 'edit_blog');
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
    | Update Blog
    |--------------------------------------------------------------------------
    */ 
    public function update_blog(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $photo = "";
            if($request->photo){
                $photo = new CURLFile($request->photo);
            }

            $url = env('Api_Base_URL')."api/update-blog";
    
            $data = array(
                'id' => $request->id,
                'title' => $request->title,
                'description' => $request->description,
                'photo' => $photo
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Blog
    |--------------------------------------------------------------------------
    */ 
    public function delete_blog(Request $request){
        $session = $request->session()->get("login_data");
        if($session){
            $id = $request->id;
            $token = $session->token;
            
            $url = env('Api_Base_URL')."api/delete-blog?id=$id";

            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }
}
