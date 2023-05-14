<?php

namespace App\Http\Controllers\Webapp;

use App\Http\Controllers\Controller;
use App\Traits\Get;
use App\Traits\Post;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    use Post;
    use Get;

    /*
    |--------------------------------------------------------------------------
    | Give Rating
    |--------------------------------------------------------------------------
    */
    public function give_rating(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $url = env('Api_Base_URL')."api/give-rating";
    
            $data = array(
                'therapist_id' => $request->therapist_id,
                'rating' => $request->rating,
                'reviews' => $request->reviews
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        } 
    }

    /*
    |--------------------------------------------------------------------------
    | Get Reviews By Therapist
    |--------------------------------------------------------------------------
    */
    public function get_reviews_by_therapist(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $id = $request->id;

            $url = getenv("Api_Base_URL")."api/get-reviews-by-therapist?id=$id";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }
}
