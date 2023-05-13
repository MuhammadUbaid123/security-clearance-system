<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /* ----------------------------------------------------------------------|
    |========================================================================|
    | Function For Getting the particular user notifications       
    |========================================================================|
    |------------------------------------------------------------------------|
    */

    public function allNotifications(Request $request)
    {
        $auth_user = Auth::user();
        /* For manual pagination */
        $page = ($request->page?:1)-1;
        $record_per_page = 10;
        $offset = $page * $record_per_page;

        $data = Notification::select("a_user.notification_count", 'b_user.photo','notifications.*')->where('notifications.user_id', $auth_user->id)
        ->leftjoin("users as a_user",'notifications.user_id','=','a_user.id')
        ->leftjoin("users as b_user",'notifications.type_id','=','b_user.id')
        ->offset($offset)->limit($record_per_page)
        ->orderBy('notifications.id', 'DESC')
        ->get();

        if($data)
        {
            foreach($data as $singleNotification){
                if($singleNotification){
                    $singleNotification->created_at_readeable_format = $this->timeago($singleNotification->created_at);
    
                    /* For Photo */
                    if($singleNotification->photo){
                        $singleNotification->photo = Storage::disk($this->storage)->url("public/images/user/150x150/").$singleNotification->photo;
                    }
                    else{
                        $singleNotification->photo = URL::to('/')."/storage/images/user/default-img.png";
                    }
                }
            }

            return response()->json([
                'status_code' => 200,
                "type" => "success",
                "data" => $data,
                'page' => $page,
                "message" => "All Notifications fetched successfully",
            ], 200);
        }


        return response()->json([
            'status_code' => 501,
            "type" => "error",
            "message" => "Operation couldn't perform!",
        ], 501);

    }

    /* ----------------------------------------------------------------------|
    |========================================================================|
    | Read all notifications       
    |========================================================================|
    |------------------------------------------------------------------------|
    */

    public function read_all_notifications(Request $request)
    {
        $auth_user = Auth::user();
        

        Notification::where('notifications.user_id', $auth_user->id)->update(["seen"=> 1]);
            
        return response()->json([
            'status_code' => 200,
            "data" => null,
            "message" => "Read all Notifications successfully",
        ], 200);
         
        
    }


    /* ----------------------------------------------------------------------|
    |========================================================================|
    | Reset the Notifications of Auth User       
    |========================================================================|
    |------------------------------------------------------------------------|
    */

    public function notifications_count_reset(Request $request){
        $auth_user = Auth::user();
        /* Reset Notification of auth user */
        $reset_notifications = User::where('users.id', $auth_user->id)
        ->where('users.id', $auth_user->id)->first();


        if($reset_notifications){
            $reset_notifications->notification_count = 0;

            $reset_notifications->save();

            return response()->json([
                'status_code' => 200,
                'type' => "success",
                "data" => $reset_notifications,
                "message" => "Notifications Reset successfully",
            ], 200);
        }

        return response()->json([
            'status_code' => 501,
            "type" => "error",
            "message" => "Some Internal Error",
        ], 501);
    }


    /* ----------------------------------------------------------------------|
    |========================================================================|
    | Reset seen in notifications       
    |========================================================================|
    |------------------------------------------------------------------------|
    */

    public function notifications_seen_reset(Request $request){
        $auth_user = Auth::user();
        
        
        $notification_number = $request->id;

        $validator = Validator::make($request->all(),[
            'id' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'type' => "error",
                "message" => "Notifications Reset successfully",
            ],400);
        }
        
        /* Reset Notification of auth user */
        $reset_notifications_seen = Notification::where('notifications.user_id', $auth_user->id)
        ->where('notifications.id', $notification_number)->first();

        if($reset_notifications_seen){
            
            $reset_notifications_seen->seen = 1;
            $reset_notifications_seen->save();

            return response()->json([
                'status_code' => 200,
                'type' => "success",
                "data" => $reset_notifications_seen,
                "message" => "Notifications Reset successfully",
            ], 200);
        }
        
    }

}
