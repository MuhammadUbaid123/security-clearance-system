<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    /* ----------------------------------------------- */
    /* =============================================== */
    /*                  Create User                     
    |* =============================================== *|
    |* ----------------------------------------------- */
    
    public function createUser(Request $request)
    {
        $auth_user = Auth::user(); 
        $permit_types = ["super_admin","admin"];
        if(!in_array($auth_user->type, $permit_types)){
            return response()->json([
                'status_code' => 401,
                "message" => "You don't have permission to access this api!",
                "data" => null
            ], 401);
        }


        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|unique:users,email',
            'user_type' => 'required',
            'phone_iso2' => 'required',
            'phone_dial_code' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
        ]);


        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                "message" => $validator->messages()->toArray(),
                "data" => null
            ], 400);
        }


        $data = User::create([
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
            'password' => bcrypt($request->password),
        ]);

        if($data)
        {

            if($request->hasFile('photo'))
            {
                $folder = '/public/images/user/';
                $original_folder = $folder."original/";
                $folder_150x150 = $folder."150x150/";
                $folder_300x300 = $folder."300x300/";
                $file = $request->file('photo');

                $stored_image = Storage::disk($this->storage)->put($original_folder, $file, 'public'); //It should be public
                $image_name = pathinfo($stored_image)['basename'];

                //Getting the extension of file
                    //Saving photo in 150x150
                $img = Image::make($file)->orientate()->resize(150, 150, function ($constraint) {$constraint->aspectRatio();});
                Storage::disk($this->storage)->put($folder_150x150.$image_name, $img->stream()->__toString(), 'public');
                
                //Saving photo in 300x300
                $img = Image::make($file)->orientate()->resize(300, 300, function ($constraint) {$constraint->aspectRatio();});
                Storage::disk($this->storage)->put($folder_300x300.$image_name, $img->stream()->__toString(), 'public');
                $user_obj = User::find($data->id);

                $user_obj->photo = $image_name;
                $user_obj->save();
                $data = $user_obj;
            }

            return response()->json([
                'status_code' => 201,
                "message" => 'Operation Performed Successfull!',
                "data" => $data
            ],201);
        }

        return response()->json([
            'status_code' => 500,
            "message" => 'Operation Could not Performed!',
            "data" => null
        ],500);
    }


    /* ----------------------------------------------- */
    /* =============================================== */
    /*                  Get single User                     
    |* =============================================== *|
    |* ----------------------------------------------- */

    public function singleUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()->toArray(),
                'data' => null
            ],400);
        }

        $data = User::find($request->id);

        if($data)
        {
            if($data->photo){
                $data->photo = Storage::disk($this->storage)->url("public/images/user/300x300/").$data->photo;
            }
            else{
                $data->photo = URL::to('/')."/storage/images/user/default-img.png";
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Operation Performed Successfully!',
                'data' => $data
            ],200);
        }

        return response()->json([
            'status_code' => 501,
            'message' => 'Operation Could not Performed!',
            'data' => null
        ],501);

    }


    /* ----------------------------------------------- */
    /* =============================================== */
    /*                  Update Single User                     
    |* =============================================== *|
    |* ----------------------------------------------- */
    public function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'fname' => 'required',
            'lname' => 'required',
            'user_type' => 'required',
            'phone_iso2' => 'required',
            'phone_number' => 'required',
            'phone_dial_code' => 'required',
        ]);


        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                "message" => $validator->messages()->toArray(),
                "data" => null
            ], 400);
        }

        $data = User::find($request->id);

        if($data)
        {
            $data->fname = $request->fname ?? $data->fname;
            $data->lname = $request->lname ?? $data->lname;
            $data->user_type = $request->user_type ?? $data->user_type;
            $data->phone_iso2 = $request->phone_iso2 ?? $data->phone_iso2;
            $data->phone_dial_code = $request->phone_dial_code ?? $data->phone_dial_code;
            $data->phone_number = $request->phone_number ?? $data->phone_number;
            $data->department = $request->department ?? $data->department;
            $data->state = $request->state ?? $data->state;
            $data->user_city = $request->user_city ?? $data->user_city;
            $data->user_address = $request->user_address ?? $data->user_address;
            $data->designation = $request->designation ?? $data->designation;


            /* Upload image */
            if($request->hasFile('photo')){
                $folder = '/public/images/user/';
                $original_folder = $folder."original/";
                $folder_150x150=$folder."150x150/";
                $folder_300x300=$folder."300x300/";
                $file = $request->file('photo');
                
                //Saving to original fromat
                $stored_image = Storage::disk($this->storage)->put($original_folder, $file, 'public');//It should be public
                $image_name = pathinfo($stored_image)['basename'];
                //Getting the extension of file
                
                //Saving photo in 150x150
                $img = Image::make($file)->orientate()->resize(150, 150, function ($constraint) {$constraint->aspectRatio();});
                Storage::disk($this->storage)->put($folder_150x150.$image_name, $img->stream()->__toString(), 'public');
                //Saving photo in 300x300
                $img = Image::make($file)->orientate()->resize(300, 300, function ($constraint) {$constraint->aspectRatio();});
                Storage::disk($this->storage)->put($folder_300x300.$image_name, $img->stream()->__toString(), 'public');
                /* Delete Previous Image */
                if($data->photo){
                    Storage::disk($this->storage)->delete($original_folder.$data->photo);
                    Storage::disk($this->storage)->delete($folder_300x300.$data->photo);
                    Storage::disk($this->storage)->delete($folder_150x150.$data->photo);
                }
                $data->photo = $image_name;
            }

            $data->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Operation Performed Successfully!',
                'data' => $data
            ],200);

        }
        return response()->json([
            'status_code' => 501,
            'message' => 'Operation Could not Performed!',
            'data' => null
        ],501);
    }


    /* ----------------------------------------------- */
    /* =============================================== */
    /*                  Update Single User                     
    |* =============================================== *|
    |* ----------------------------------------------- */
}
