<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /* ----------------------------------------------- */
    /* =============================================== */
    /*                Signup                     
    |* =============================================== *|
    |* ----------------------------------------------- */

    public function signup_api(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'fname' => 'required',
            'lname' => 'required',
            'user_type' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'type' => 'error',
                "message" => $validator->messages()->toArray(),
                "data" => null
            ], 400);
        }

        /* If user type is staff and not filled the cnic */
        if(($request->user_type=='staff' || $request->user_type == 'concerned_person') && !$request->cnic)
        {
            return response()->json([
                'status_code' => 400,
                'type' => 'error',
                "message" => "CNIC can't be empty!",
                "data" => null
            ], 400);
        }

        /* if user type is student and not filled the email orr filled incorrect format */
        if($request->user_type=='student')
        {
            Validator::extend('email', function ($attribute, $value, $parameters, $validator) {
                $regex = '/^(19|20|21|22|23)-(CS|ME|EE)-(017|001|023|043)$/i';
            
                return preg_match($regex, $value);
            });
            
            // Usage:
            $validator_mail = Validator::make($request->all(), [
                'email' => 'required|unique:users,email',
            ]);
            if ($validator_mail->fails()) {

                return response()->json([
                    'status_code' => 400,
                    'type' => 'error',
                    "message" => "Email format is in-correct!",
                    "data" => null
                ], 400);
            }
        }

        $data = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'user_type' => $request->user_type,
            'designation' => $request->designation,
            'cnic' => $request->cnic,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);


        if(!$data){
            return response()->json([
                'status_code' => 501,
                'type' => 'error',
                "message" => "Unable to perform the signup action, try-again later!",
                "data" => null
            ], 501);
        }

        return response()->json([
            'status_code' => 200,
            "data" => $data,
            "message" => "Operation Performed successfully"
        ], 200);

    }
    /* ----------------------------------------------- */
    /* =============================================== */
    /*                User Login                     
    |* =============================================== *|
    |* ----------------------------------------------- */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status_code' => 400,
                'type' => 'error',
                "message" => $validator->messages()->toArray(),
                "data" => null
            ], 400);
        }
        /* If user type is staff and not filled the cnic */
        if(($request->user_type=='staff' || $request->user_type == 'concerned_person') && !$request->cnic)
        {
            return response()->json([
                'status_code' => 400,
                'type' => 'error',
                "message" => "CNIC can't be empty!",
                "data" => null
            ], 400);
        }

        /* if user type is student and not filled the email orr filled incorrect format */
        // if($request->user_type=='student' && $request->user_type !== 'admin')
        // {
        //     Validator::extend('email', function ($attribute, $value, $parameters, $validator) {
        //         $regex = '/^(19|20|21|22|23)-(CS|ME|EE)-(017|001|023|043)@student\.hitecuni\.edu\.pk$/i';
            
        //         return preg_match($regex, $value);
        //     });
            
        //     $validator_mail = Validator::make($request->all(), [
        //         'email' => 'required|unique:users,email',
        //     ]);
        //     if ($validator_mail->fails()) {
        //         return response()->json([
        //             'status_code' => 400,
        //             'type' => 'error',
        //             "message" => "Email format is in-correct!",
        //             "data" => null
        //         ], 400);
        //     }
        // }

        $loginWith = "email";
        if($request->user_type=='staff' || $request->user_type == 'concerned_person'){
            $loginWith = 'cnic';
        }

        $loginField = ($loginWith == 'email') ? 'email' : 'cnic';
        $loginValue = ($loginWith == 'email') ? $request->email : $request->cnic;
        $data = [
            $loginField => $loginValue,
            'password' => ($request->password),
        ];

        if(auth()->attempt($data)) {
            $user = User::select('users.*')
            ->where([$loginField => $loginValue])
            ->first();

            if($user){
                /* Account is not Approved */
                if($user->status!==1 && $user->user_type !== 'admin'){
                    return response()->json([
                        'status_code' => 402,
                        "message" => "Your account is pending to be activated by administration",
                        "data" => null,
                    ], 402);
                }
                
                /* Generating the oauth token */
                $token = $user->createToken('Security Clearance')->accessToken;
                $user->token = $token;
                if($user->photo){
                    $user->photo = Storage::disk($this->storage)->url("public/images/user/300x300/").$user->photo;
                }
                else{
                    $user->photo = URL::to('/')."/storage/images/user/default-img.png";
                    
                }
                /* Generating hash for security reasons */
                return response()->json([
                    'status_code' => 200,
                    "data" => $user,
                    "message" => "Operation Performed successfully"
                ], 200);
                
            }
            else{
                return response()->json([
                    'status_code' => 401,
                    "message" => "You're not authorized to access this application!",
                    'data' => null,
                ], 401);
            }
        } else {
            return response()->json([
                'status_code' => 404,
                "message" => ucfirst($loginWith)." or password is incorrect!",
                "data" => null
            ],404);
        }
    }


    /* ----------------------------------------------- */
    /* =============================================== */
    /*                  User Logout                    
    |* =============================================== *|
    |* ----------------------------------------------- */

    public function logout(){
        $user = auth()->user()->token();
        $user->revoke();
        return response()->json([
            'status_code' => 200,
            "message" => "Operation Performed Successfully!"
        ], 200);
    }
}
