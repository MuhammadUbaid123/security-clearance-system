<?php

use App\Http\Controllers\Webapp\AuthController;
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

    /*
    |--------------------------------------------------------------------------
    | User Tab
    |--------------------------------------------------------------------------
    */
    Route::get('/', [UserController::class, 'show_all_users'])->name('home');        
    /* Show Create User */
    Route::get('/create-user', [UserController::class, 'show_create_user'])->name('createuser');
    /* Create User (API Call) */
    Route::post('/create-user', [UserController::class, 'create_user']);

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
    
});
