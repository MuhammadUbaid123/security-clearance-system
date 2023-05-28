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
            $users = User::select('users.id')->whereNotIn('users.user_type', ['student', 'staff'])->get();

            
            for($i=0; $i<count($users); $i++){

                $approved_bies = ApprovedBy::create([
                    'user_id' => $authUser?->id,
                    'name' => $authUser?->fname." ".$authUser?->lname,
                    'clear_req_id' => $data?->id,
                    'approver_id' => $users[$i]?->id
                ]);

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
        $permit_types = ['super_admin', 'concerned_person'];

        if(!in_array($authUser->user_type, $permit_types)){
            return response()->json([
                'status_code' => 401,
                'type'=> 'error',
                "message" => "You don't have permission to access this api!",
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'request_id' => 'required',
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

        $reqdata = ClearanceRequest::where('id', $request->request_id)->first();

        if($reqdata)
        {
            $exist = ApprovedBy::where('clear_req_id', $reqdata->id)->where('approver_id', $authUser->id)->first();

            // return $exist;exit;
            if($exist)
            {
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
                    }
                    $exist->comments = $request->comments ?? $exist->comments;
                    $exist->miscellaneous = $request->miscellaneous ?? $exist->miscellaneous;

                    
                }else{
                    $exist->request_status = $request->request_status;
                }
                $exist->save();

                if($exist->request_status == 'accepted')
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
                    'data' => [
                        'status' => $exist->status==1?'Approved':'Rejected',
                    ]
                ],200);
            }

            // if($data)
            // {
            //     if($data->status == 1)
            //     {
            //         $reqdata->approvedd_by_count++;
            //         $reqdata->save();
            //     }

            //     if($reqdata->approvedd_by_count == $reqdata->req_to_members)
            //     {
            //         $reqdata->request_status = 1;
            //         $reqdata->save();
            //     }
            //     return response()->json([
            //         'status_code' => 200,
            //         'type'=> 'success',
            //         'message' => 'Operation Performed Successfully!',
            //         'data' => [
            //             'status' => $data->status==1?'Approved':'Rejected',
            //         ]
            //     ],200);
            // }


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

    /* All clearance requests */
    public function allrequest(Request $request)
    {
        $authUser = Auth::user();

        $permit_types = ['student', 'staff'];

        // if(in_array($authUser->user_type, $permit_types)){
        //     return response()->json([
        //         'status_code' => 401,
        //         'type'=> 'error',
        //         "message" => "You don't have permission to access this api!",
        //     ], 401);
        // }
        /* For manual pagination */
        $page = ($request->page?:1)-1;
        $record_per_page = 10;
        $offset = $page * $record_per_page;
        
        $where = [];

        if(in_array($authUser->user_type, $permit_types))
        {
            $where = [
                'requester_id' => $authUser->id
            ];
        }


        $allReqs = ClearanceRequest::select('clearance_requests.requester_id', 'clearance_requests.session', 'clearance_requests.request_status', 'users.*')->where($where)->leftJoin('users', 'users.id', 'clearance_requests.requester_id');

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
                $query->orWhere("clearance_requests.session", "like", "%".$search."%");
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
