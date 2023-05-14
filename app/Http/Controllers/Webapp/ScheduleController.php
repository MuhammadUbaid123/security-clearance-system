<?php

namespace App\Http\Controllers\Webapp;

use App\Http\Controllers\Controller;
use App\Traits\Delete;
use App\Traits\Get;
use App\Traits\Post;
use Illuminate\Http\Request;
use CURLFile;
use Illuminate\Support\Facades\Mail;


class ScheduleController extends Controller
{
    use Post;
    use Get;
    use Delete;

    /*
    |--------------------------------------------------------------------------
    | Create Schedule
    |--------------------------------------------------------------------------
    */ 
    public function show_create_schedule(Request $request){
        $session = $request->session()->get("login_data");

        if($session && $session->type == 'admin'){
            return view('schedules.createschedule')
            ->with('session', $session)
            ->with('parent_tab', 'schedules')
            ->with('tab_name', 'create_schedule');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Schedule Api
    |--------------------------------------------------------------------------
    */ 
    public function create_schedule(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $url = env('Api_Base_URL')."api/create-schedule";
    
            $data = array(
                'schedule_date' => $request->schedule_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'schedule_duration' => $request->schedule_duration, 
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }       
    }

    /*
    |--------------------------------------------------------------------------
    | Show All Schedules
    |--------------------------------------------------------------------------
    */ 
    public function show_all_schedules(Request $request){
        $session = $request->session()->get("login_data");

        if($session && $session->type == 'super_admin' || $session->type == 'admin'){
            return view('schedules.allschedules')
            ->with('session', $session)
            ->with('parent_tab', 'schedules')
            ->with('tab_name', 'all_schedules');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Schedules Api
    |--------------------------------------------------------------------------
    */ 
    public function get_all_schedules(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $count = $request->page;

            $url = getenv("Api_Base_URL")."api/get-all-schedules?page=$count";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update Schedule Status Api
    |--------------------------------------------------------------------------
    */
    public function update_schedule_stauts(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;

            $url = getenv("Api_Base_URL")."api/update-schedule-status";

            $data = array(
                'id' => $request->id,
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Schedules By Therapist Api
    |--------------------------------------------------------------------------
    */
    public function get_schedules_by_therapist(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $id = $request->id;

            $url = getenv("Api_Base_URL")."api/get-schedules-by-therapist?id=$id";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Schedule
    |--------------------------------------------------------------------------
    */ 
    public function delete_schedule(Request $request){
        $session = $request->session()->get("login_data");
        if($session){
            $id = $request->id;
            $token = $session->token;
            
            $url = env('Api_Base_URL')."api/delete-schedule?id=$id";

            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Book Session Api
    |--------------------------------------------------------------------------
    */ 
    public function book_session(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $url = env('Api_Base_URL')."api/book-session";
    
            $data = array(
                'therapist_id' => $request->therapist_id,
                'schedule_id' => $request->schedule_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                // 'fees' => $request->fees,
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        }       
    }

    /*
    |--------------------------------------------------------------------------
    | Show All Sessions
    |--------------------------------------------------------------------------
    */ 
    public function show_all_sessions(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            return view('sessions.allsessions')
            ->with('session', $session)
            ->with('parent_tab', 'sessions')
            ->with('tab_name', 'all_sessions');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Sessions Api
    |--------------------------------------------------------------------------
    */ 
    public function get_all_sessions(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $count = $request->page;

            $url = getenv("Api_Base_URL")."api/get-all-sessions?page=$count";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Show My Sessions
    |--------------------------------------------------------------------------
    */ 
    public function show_my_sessions(Request $request){
        $session = $request->session()->get("login_data");

        if($session && $session->type == 'user'){
            return view('sessions.mysessions')
            ->with('session', $session)
            ->with('parent_tab', 'sessions')
            ->with('tab_name', 'my_sessions');
        }
        else{
            return redirect("404");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get My Sessions Api
    |--------------------------------------------------------------------------
    */ 
    public function get_my_sessions(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $count = $request->page;

            $url = getenv("Api_Base_URL")."api/get-my-sessions?page=$count";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | Update Session Status
    |--------------------------------------------------------------------------
    */
    public function update_session_status(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;

            $url = getenv("Api_Base_URL")."api/update-session-status";

            $data = array(
                'id' => $request->id,
                'session_status' => $request->session_status,
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            $data = json_decode($response);

            if($data->status_code == 200){
                $sender_email = $session->email;
                $email_subject = "Session Confirmation Email";
                
                $session_status = $data->data->session_status;
                if($session_status == 'booked'){
                    $message = "Your session has been approved successfully";
                }
                else if($session_status == 'completed'){
                    $message = "Your session has been completed successfully";
                }
                else if($session_status == 'cancelled'){
                    $message = "Your session has been cancelled";
                }

               $patient_name = $data->data->name;
               $patient_email = $data->data->email;

                $content = [
                    'name' => $patient_name,
                    'email' => $patient_email,
                    'message' => $message
                ];

                Mail::to($patient_email)->send(new \App\Mail\ApproveSessionEmail($email_subject, $sender_email, $content));
            }

            return $response;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Test Data Api
    |--------------------------------------------------------------------------
    */
    public function get_test_data(Request $request){
        $session = $request->session()->get("login_data");

        if($session){
            $token = $session->token;
            $id = $request->id;

            $url = getenv("Api_Base_URL")."api/get-test-data?id=$id";
    
            $response = $this->curlGet_token($url, $token);
            return $response;
        }
    }
    
    /*
    |--------------------------------------------------------------------------
    | Create Test Api
    |--------------------------------------------------------------------------
    */
    public function create_test(Request $request){
        $session = $request->session()->get("login_data");
        
        if($session){
            $token = $session->token;

            $therapist_file = "";
            if($request->therapist_file){
                $therapist_file = new CURLFile($request->therapist_file);
            }

            $patient_file = "";
            if($request->patient_file){
                $patient_file = new CURLFile($request->patient_file);
            }

            
            $url = env('Api_Base_URL')."api/create-test";
    
            $data = array(
                'session_id' => $request->session_id,
                'therapist_id' => $request->therapist_id,
                'therapist_question' => $request->therapist_question,
                'therapist_file' => $therapist_file, 
                'patient_id' => $request->patient_id, 
                'patient_answer' => $request->patient_answer, 
                'patient_file' => $patient_file, 
                'session_status' => $request->session_status,
            );
    
            $response = $this->curlPost_token($url, $data, $token);
            return $response;
        } 
    }
}
