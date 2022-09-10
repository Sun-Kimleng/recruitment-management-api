<?php

use App\Http\Controllers\admins\JobController;
use App\Http\Controllers\candidates\CandidateController;
use App\Http\Controllers\ForgetpasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\FacebookController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Authenticated for all users
Route::middleware(['auth:api'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        Route::post('/logout',[UserController::class, 'logout'])->name('logout');
        Route::get('/checkAuth', function(){return response()->json(['message'=>'You are authenticated', 'status'=>200]);});
        
            //////////////////////////////////////////////////////////////////
            //////////                 Admin Zone                ////////////
            ////////////////////////////////////////////////////////////////

        //Admin Zone
        Route::middleware(['scope:admin'])->group(function(){
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
        //////////////////////////////////////////////////////////////////
        //////////                Candidate Zone             ////////////
        ////////////////////////////////////////////////////////////////
        Route::middleware(['scope:candidate'])->group(function(){
            //Candidate General
            Route::apiResource('/candidate', CandidateController::class);

            //Candidate insert education
            Route::post('/candidate/insert_education', [CandidateController::class, 'insertEducation']);
            
        });
});


//Authenticated For Admin
Route::middleware(['auth:api', 'verified', 'scope:admin'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        
    });
});

//Login and Register User
Route::prefix('/user')->name('user.')->group(function(){
    Route::post('/create', [UserController::class, 'create'])->name('create');
    Route::post('/login',[UserController::class, 'login'])->name('login');
});

//Login and Register For Candidates
Route::prefix('/candidate')->name('candidate.')->group(function(){
    Route::post('/create', [CandidateController::class, 'create'])->name('create');
});

//Email Verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

//Forget Password
Route::post('forget-password', [ForgetpasswordController::class, 'forgetPassword']);
Route::post('reset-password', [ForgetpasswordController::class, 'reset']);

//Candidate Login with Facebook
Route::get('login/facebook/url', [FacebookController::class, 'loginUrl']);
Route::get('login/facebook/callback', [FacebookController::class, 'loginCallback']);