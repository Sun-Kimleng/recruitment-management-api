<?php

use App\Http\Controllers\admins\JobController;
use App\Http\Controllers\ForgetpasswordController;
use App\Http\Controllers\NewEmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Authenticated for all users
Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        Route::post('/logout',[UserController::class, 'logout'])->name('logout');
        Route::get('/checkAuth', function(){return response()->json(['message'=>'You are authenticated', 'status'=>200]);});
        
        //User Information
        Route::get('/detail', [UserController::class, 'index']);

        //User Update Email
        Route::post('/update_email', [UserController::class, 'updateEmail']);

        //User Update Username
        Route::post('/update_username', [UserController::class, 'updateUsername']);

        //User Change Password
        Route::post('/change_password', [UserController::class, 'changePassword']);

        //User Change Avatar
        Route::post('/change_avatar', [UserController::class, 'changeAvatar']);
        
        //User Admin Creates Users
        Route::post('/admin_creates_users', [UserController::class, 'adminCreatesUsers']);

        //User Get Admin,Editor,Moderator
        Route::get('/get_admin', [UserController::class, 'getAdmin']);
        Route::get('/get_editor', [UserController::class, 'getEditor']);
        Route::get('/get_moderator', [UserController::class, 'getModerator']);

        //User Edit User Role
        Route::put('/edit_user_role/{id}', [UserController::class, 'editUserRole']);

        //User Delete User
        Route::post('/delete_user/{id}', [UserController::class, 'deleteUser']);

        //User Deactivate and Reactviate Account
        Route::put('/deactivate_user/{id}', [UserController::class,  'deactivateAccount']);

        //User Get User By Id
        Route::get('/get_user_by_id/{id}', [UserController::class, 'getUserById']);

        //Job
        Route::delete('/job/deleteAll/{id}', [JobController::class, 'deleteAll']);
        Route::apiResource('/job', JobController::class);
        
    });
});

//Authenticated For Admin
Route::middleware(['auth:sanctum', 'verified', 'isAdmin'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        
    });
});

//Login and Register User
Route::prefix('/user')->name('user.')->group(function(){
    Route::post('/create', [UserController::class, 'create'])->name('create');
    Route::post('/login',[UserController::class, 'login'])->name('login');
});

//Email Verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

//Forget Password
Route::post('forget-password', [ForgetpasswordController::class, 'forgetPassword']);
Route::post('reset-password', [ForgetpasswordController::class, 'reset']);


