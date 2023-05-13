<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovedBy;
use App\Models\ClearanceRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CleranceRequestController extends Controller
{

    /* Create Cleranace Request */
    public function createRequest(Request $request)
    {
        $authUser = Auth::user();


        $validator = Validator::make($request->all(), [
            'st_session' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'type'=> 'error',
                'message' => $validator->messages()->toArray(),
            ],400);
        }

        $alreadyRequested = ClearanceRequest::where('requester_id', $authUser->id)->first();
        if($alreadyRequested)
        {
            return response()->json([
                'status_code' => 409,
                'type'=> 'error',
                'message' => 'Already requested for Clearance!',
            ],409);
        }

        $req_to_members = User::select(DB::raw('count(distinct user_type)'))
        ->whereNotIn('user_type', ['student', 'staff'])
        ->count();

        $data = ClearanceRequest::create([
            'session' => $request->st_session,
            'requester_id' => $authUser?->id,
            'req_to_members' => $req_to_members??0,
            'approvedd_by_count' => 0,
        ]);

        if($data)
        {
            $users = User::select('users.id')->whereNot('users.user_type', 'student')->get();

            
            for($i=0; $i<count($users); $i++){

                /* Creating Notification */
                $create_notification = Notification::create([
                    'name' => $authUser->fname." ".$authUser->lname." Requested for Clearance!",
                    'type' => "Clearance",
                    'type_id' => $authUser->id,
                    'user_id' => $users[$i]->id,
                ]);


                if($create_notification)
                {
                    $dataObj = User::select("users.notification_count", "users.id")->where('users.id','=', $users[$i]->id)->first();
                    $dataObj->notification_count++;
                    $dataObj->save();
                }
            }
            return response()->json([
                'status_code' => 201,
                'type'=> 'success',
                'message' => 'Operation Performed Successfully!',
                'data' => $data
            ],201);
        }


        return response()->json([
            'status_code' => 501,
            'type'=> 'error',
            'message' => 'Operation Could not perform!',
        ],501);
        
    }

    /* Get single Clearance Request */
    public function singleClearance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'type'=> 'error',
                'message' => $validator->fails(),
            ],400);
        }


        $data = ClearanceRequest::whhere('id', $request->request_id)->first();

        if($data)
        {
            return response()->json([
                'status_code' => 200,
                'type'=> 'success',
                'message' => 'Operation Performed Successfully!',
                'data' => $data
            ],200);
        }


        return response()->json([
            'status_code' => 501,
            'type'=> 'error',
            'message' => 'Operation Could not perform!',
        ],501);

    }


    /* Approve clearance request */
    public function actionOnClearaceReq(Request $request)
    {
        $authUser = Auth::user();
        $validator = Validator::make($request->all(), [
            'request_id' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'type'=> 'error',
                'message' => $validator->messages()->toArray(),
            ],400);
        }

        $reqdata = ClearanceRequest::where('id', $request->request_id)->where('')->first();

        if($reqdata)
        {
            $data = ApprovedBy::create([
                'user_id' => $reqdata->requester_id,
                'name' => $authUser->fname." ".$authUser->lname,
                'clear_req_id' => $reqdata->id,
                'comments' => $request->comments,
                'status' => $request->status,
            ]);

            if($data)
            {
                if($data->status == 1)
                {
                    $reqdata->approvedd_by_count++;
                    $reqdata->save();
                }

                if($reqdata->approvedd_by_count == $reqdata->req_to_members)
                {
                    $reqdata->request_status = 1;
                    $reqdata->save();
                }
                return response()->json([
                    'status_code' => 200,
                    'type'=> 'success',
                    'message' => 'Operation Performed Successfully!',
                ],200);
            }


            return response()->json([
                'status_code' => 501,
                'type'=> 'error',
                'message' => 'Operation could not Perform!',
            ],501);
        }

        return response()->json([
            'status_code' => 404,
            'type'=> 'error',
            'message' => 'Request Not Found!',
        ],404);

    }
}
