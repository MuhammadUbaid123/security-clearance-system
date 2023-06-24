<?php

use App\Http\Controllers\Webapp\AuthController;
use App\Http\Controllers\Webapp\ClearanceController;
use App\Http\Controllers\Webapp\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['is_login']], function (){

    /* Show Signup */
    Route::get('/signup', [AuthController::class, 'show_signup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);

    /* Account Signin */
    Route::get('/signin', [AuthController::class, 'show_signin'])->name('signin');
    Route::post('/signin', [AuthController::class, 'signin']);
});

/* Logout */
// Route::get('/logout', function(){
//     Session::flush();
//     Session::forget('login_data');
//     return redirect()->route('signin');
// })->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['login_user']], function (){

    // Returning a view of Dashboard from controller by making route here 
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    /*
    |--------------------------------------------------------------------------
    | User Tab
    |--------------------------------------------------------------------------
    */
    Route::get('/', [UserController::class, 'show_all_users'])->name('home');        

    /* Show All Users */
    Route::get('/all-users', [UserController::class, 'show_all_users'])->name('allusers');
    /* Get All Users (API Call) */
    Route::post('/get-all-users', [UserController::class, 'get_all_users']);

    /* Show Edit User */
    Route::get('/edit-user/{id?}', [UserController::class, 'show_edit_user'])->name('edituser');
    /* Update User (API Call) */
    Route::post('/update-user', [UserController::class, 'update_user']);

    /* Change User Status (API Call) */
    Route::post('/change-user-status', [UserController::class, 'change_user_status']);

    /* Delete User (API Call) */
    Route::post('/delete-user', [UserController::class, 'delete_user']);


    /*
    |--------------------------------------------------------------------------
    | Clearance Tab
    |--------------------------------------------------------------------------
    */
    /* Show Create Clearance */
    Route::get('/create-request', [ClearanceController::class, 'show_create_clearance'])->name('createclearance');
    /* Create Clearance (API) */
    Route::post('/create-clearance', [ClearanceController::class, 'create_clearance']);

    /* Show All Requests */
    Route::get('/all-requests', [ClearanceController::class, 'show_all_requests'])->name('allrequests');
    /* Get All requests (API Call) */
    Route::post('/get-all-requests', [ClearanceController::class, 'get_all_requests']);

    /* Change Request Status (API Call) */
    Route::post('/action-on-request', [ClearanceController::class, 'change_request_status']);

    /* View Challan Form */
    Route::get('/challan', [ClearanceController::class, 'show_challan'])->name('challan');

    /* Show User Settings */
    Route::get('/settings', [UserController::class, 'show_edit_settings'])->name('settings');
    /* Update User Settings (API Call) */
    Route::post('/update-user-settings', [UserController::class, 'update_user_settings']);
});
