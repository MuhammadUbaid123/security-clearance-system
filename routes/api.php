<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


/*
|--------------------------------------------------------------------------
| Authentication APIS
|--------------------------------------------------------------------------
|*/
Route::get('/login', [AuthController::class, 'login']); // Login APi


/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/


/* Create User */
Route::post('/create-user', [UserController::class, 'createUser'])->middleware('auth:api');
/* Get single User */
Route::get('/single-user', [UserController::class, 'singleUser']);
/* Update User */
Route::post('/update-user', [UserController::class, 'updateUser']);
/* Change User Status */
Route::get('/change-user-status', [UserController::class, 'changeUserStatus']);
/* Delete Single user */
Route::delete('/delete-user', [UserController::class, 'deleteUser']);
/* All users */
Route::get('/all-users', [UserController::class, 'allUsers'])->middleware("auth:api");

