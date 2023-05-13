<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClearanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $data = ClearanceRequest::create([
            'session' => $request->st_session,
            'requester_id' => $authUser?->id,
            'req_to_members' => 0,
            'approvedd_by_count' => 0,
        ]);

        if($data)
        {
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
}
