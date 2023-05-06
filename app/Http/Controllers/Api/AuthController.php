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
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status_code' => 400,
                "message" => $validator->messages()->toArray(),
                "data" => null
            ], 400);
        }
        
            $data = [
                'email' => $request->email,
                'password' => ($request->password),
            ];

            if(auth()->attempt($data)) {
                $user = User::select('users.*')
                ->where(['email' => $request->email])
                ->first();
                
                if($user){
                    
                    /* Account is not Approved */
                    if($user->status!==1){
                        return response()->json([
                            'status_code' => 402,
                            "message" => "sorry! Your account isn't Activated!",
                            "data" => null,
                        ], 402);
                    }
                   
                    /* Generating the oauth token */
                    // return $user;
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
                    'status_code' => 500,
                    "message" => "There is Some Internal Error!",
                    "data" => null
                ],500);
            }
    }
}
