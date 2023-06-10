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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class CleranceRequestController extends Controller
{

    /* Create Cleranace Request */
    public function createRequest(Request $request)
    {
        $authUser = Auth::user();

        $permit_types = ['student', 'staff'];

        if(!in_array($authUser->user_type, $permit_types)){
            return response()->json([
                'status_code' => 401,
                'type'=> 'error',
                "message" => "You don't have permission to access this api!",
            ], 401);
        }


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

        /* requester id is basically a staff or student user id */
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
            $users = User::select('users.*')->where(function ($query) {
                $query->where('users.user_type', 'concerned_person')
                    ->orWhere('users.user_type', 'admin');
            })->get();

            
            foreach($users as $obj){

                ApprovedBy::create([
                    'user_id' => $authUser->id,
                    'name' => $obj->fname." ".$obj->lname,
                    'clear_req_id' => $data->id,
                    'session' => $data->session,
                    'approver_id' => $obj->id
                ]);

                /* Creating Notification */
                $create_notification = Notification::create([
                    'name' => $authUser->fname." ".$authUser->lname." Requested for Clearance!",
                    'type' => "Clearance",
                    'type_id' => $authUser->id,
                    'user_id' => $obj->id,
                ]);


                if($create_notification)
                {
                    $dataObj = User::select("users.notification_count", "users.id")->where('users.id','=', $obj->id)->first();
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
        $authUser = Auth::user();

        $permit_types = ['super_admin', 'concerned_person'];

        if(!in_array($authUser->user_type, $permit_types)){
            return response()->json([
                'status_code' => 401,
                'type'=> 'error',
                "message" => "You don't have permission to access this api!",
            ], 401);
        }
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


        /* fetch single clearance request */
        $data = ClearanceRequest::where('id', $request->request_id)->first();

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
        $permit_types = ['admin', 'concerned_person'];

        if(!in_array($authUser->user_type, $permit_types)){
            return response()->json([
                'status_code' => 401,
                'type'=> 'error',
                "message" => "You don't have permission to access this api!",
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'request_status' => 'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status_code' => 400,
                'type'=> 'error',
                'message' => $validator->messages()->toArray(),
            ],400);
        }

        $reqdata = ClearanceRequest::where('requester_id', $request->user_id)->first();

        if($reqdata)
        {
            $exist = ApprovedBy::where('clear_req_id', $reqdata->id)->where('approver_id', $authUser->id)->first();

            if($exist)
            {
                if($exist->request_status == 'pending'){
                    $exist->name = $authUser->fname." ".$authUser->lname;
                    if($request->request_status=='rejected')
                    {
                        if(!$request->comments && !$request->miscellaneous)
                        {
                            return response()->json([
                                'status_code' => 400,
                                'type'=> 'error',
                                'message' => 'Comments or miscellaneous cant be empty!',
                            ],400);
                        } else{
                            $exist->comments = $request->comments ?? $exist->comments;
                            $exist->miscellaneous = $request->miscellaneous ?? $exist->miscellaneous;
                        }
                    } 
                    $exist->request_status = $request->request_status;
                    $exist->status = $request->request_status=='approved' ? 1 : 0;
                    $exist->save();

                    if($exist->request_status == 'approved')
                    {
                        $reqdata->approvedd_by_count++;
                    }
                    if($reqdata->approvedd_by_count == $reqdata->req_to_members)
                    {
                        $reqdata->request_status = 'approved';
                    }
                    $reqdata->save();

                    return response()->json([
                        'status_code' => 200,
                        'type'=> 'success',
                        'message' => 'Operation Performed Successfully!',
                        'data' => [
                            'status' => $exist->request_status == 'approved' ?'approved':'rejected',
                            'req_to_members' => $reqdata->req_to_members,
                            'approvedd_by_count' => $reqdata->approvedd_by_count,
                        ]
                    ],200);
                } else{
                    return response()->json([
                        'status_code' => 404,
                        'type'=> 'error',
                        'message' => 'You cannot change your request anymore',
                    ],404);
                }
            }

            return response()->json([
                'status_code' => 403,
                'type'=> 'error',
                'message' => 'Operation could not Perform!',
            ],403);
        }

        return response()->json([
            'status_code' => 404,
            'type'=> 'error',
            'message' => 'Request Not Found!',
        ],404);
    }

    /* All clearance requests */
    public function allrequest(Request $request)
    {
        $authUser = Auth::user();

        /* For manual pagination */
        $page = ($request->page?:1)-1;
        $record_per_page = 10;
        $offset = $page * $record_per_page;
        
        $where = [];
        $allReqs = [];
        $searchBySession = "";

        if($authUser->user_type == 'student' || $authUser->user_type == 'staff')
        {
            $where = [
                'requester_id' => $authUser->id
            ];
            $allReqs = ClearanceRequest::select('clearance_requests.requester_id', 'clearance_requests.session', 'clearance_requests.request_status', 'clearance_requests.approvedd_by_count', 'clearance_requests.req_to_members', 'users.*')
            ->where($where)
            ->leftJoin('users', 'users.id', 'clearance_requests.requester_id');
            // $searchBySession = "clearance_requests.session";
        } 
        else if ($authUser->user_type == 'concerned_person' || $authUser->user_type == 'admin'){
            $where = [
                'approved_bies.approver_id' => $authUser->id
            ];
            $allReqs = ApprovedBy::select('approved_bies.user_id', 'approved_bies.session', 'approved_bies.request_status', 'clearance_requests.approvedd_by_count', 'clearance_requests.req_to_members', 'users.*')
            ->where($where)
            ->leftJoin('users', 'users.id', 'approved_bies.user_id')
            ->leftJoin('clearance_requests', 'approved_bies.clear_req_id', 'clearance_requests.id');
            // $searchBySession = "approved_bies.session";
        }

        $search = $request->search;
        if($search)
        {
            $allReqs->where(function($query) use ($search) {//Group all where queries

                $query->where("users.id", "like", "%".$search."%");
                $query->orWhere(DB::raw("concat_ws(' ',users.fname,users.lname)"), "like", "%".$search."%");
                $query->orWhere(DB::raw("concat(users.fname,users.lname)"), "like", "%".$search."%");
                $query->orWhere("users.email", "like", "%".$search."%");
                $query->orWhere(DB::raw("concat_ws(' ',users.phone_dial_code,users.phone_number)"), "like", "%".$search."%");
                $query->orWhere(DB::raw("concat(users.phone_dial_code,users.phone_number)"), "like", "%".$search."%");
                $query->orWhere("users.user_city", "like", "%".$search."%");
                $query->orWhere("users.department", "like", "%".$search."%");
                $query->orWhere("users.user_type", "like", "%".$search."%");
                $query->orWhere("users.designation", "like", "%".$search."%");
                // $query->orWhere($searchBySession, "like", "%".$search."%");
            }); 
        }

        $total_users = $allReqs->count();
        $allReqs = $allReqs->orderBy("users.id", "desc")
            ->offset($offset)
            ->limit($record_per_page)
            ->groupBy('id')
            ->get();


        if($allReqs)
        {
            foreach($allReqs as $user){
                    
                /* For Photo */
                if($user->photo){
                    $user->photo = Storage::disk($this->storage)->url("images/user/300x300/").$user->photo;
                }
                else{
                    $user->photo = URL::to('/')."/storage/images/user/default-img.png";
                }    
            }

            return response()->json([
                'status_code' => 200,
                'type' => 'success',
                "message" => "Operation Performed successfully",
                'total_users' => $total_users,
                "page"=>$page+1,
                "data" => $allReqs,
            ], 200);
        }

        return response()->json([
            'status_code' => 501,
            'type' => 'error',
            'message' => "Operation Couldn't Perform!",
            'data' => null
        ],501);

    }
}
