<?php

use App\Http\Controllers\admins\JobController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\candidates\CandidateController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ForgetpasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusController;

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
        Route::middleware(['scope:admin,editor,moderator'])->group(function(){
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

            //Candidate get ALl Candidate
            Route::post('/get_all_candidates', [CandidateController::class, 'getAllCandidates']);

            //Candidate get candidate details
            Route::get('/get_candidate/{id}', [CandidateController::class, 'show']);

            //Candidate get candidate details
            Route::post('/get_candidate/search', [CandidateController::class, 'searching']);

            //Post
            Route::apiResource('/post', PostController::class);
            Route::post('/get_post', [PostController::class, 'getAllPost']);
            Route::put('/status/change_status/{id}', [PostController::class, 'changeStatus']);
            
            //Status 
            Route::get('/status/all', [StatusController::class, 'getAllStatus']);
            Route::apiResource('/status', StatusController::class);
            

            //Job Applied 
            Route::post('/apply/all', [ApplyController::class, 'getAllApply']);
            Route::apiResource('/apply', ApplyController::class);

            //Company Info
            Route::apiResource('/company',CompanyController::class);

            //Report
            Route::apiResource('/report',ReportController::class);
           
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
            //Candidate insert skill
            Route::post('/candidate/insert_skill', [CandidateController::class, 'insertSkill']);
            //Candidate insert experiences
            Route::post('/candidate/insert_experience', [CandidateController::class, 'insertExperience']);
            //Candidate insert languages
            Route::post('/candidate/insert_language', [CandidateController::class, 'insertLanguage']);
            //Candidate insert cv
            Route::post('/candidate/upload_cv', [CandidateController::class, 'uploadCv']);

            //Candidate delete cv
            Route::post('/candidate/delete_cv', [CandidateController::class, 'deleteCv']);

            //Candidate update description
            Route::post('/candidate/update_description', [CandidateController::class, 'updateDescription']);

            //Candidate update contact
            Route::post('/candidate/update_contact', [CandidateController::class, 'updateContact']);

            //Candidate update appearance
            Route::post('/candidate/update_appearance', [CandidateController::class, 'updateAppearance']);

            //Candidate update overview
            Route::post('/candidate/update_overview', [CandidateController::class, 'updateOverview']);

            //Candidate update job status
            Route::post('/candidate/update_job_status', [CandidateController::class, 'updateJobStatus']);
        
            //Apply Job
            Route::get('/candidate/apply/{id}', [ApplyController::class, 'checkIfApplied']);
            Route::post('/candidate/apply', [ApplyController::class, 'store']);
            Route::delete('/candidate/apply/{id}', [ApplyController::class, 'destroy']);
            Route::get('/candidate/apply/all/{id}', [ApplyController::class, 'getMyApplies']);

    });  
});


//Get all post
Route::post('/get_post', [PostController::class, 'getAllPostForCandidate']);
Route::get('/get_post/{id}', [PostController::class, 'show']);

//Authenticated For Admin
Route::middleware(['auth:api', 'verified', 'scope:admin'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        
    });
});


//Company info
Route::get('/comp',[CandidateController::class, 'candidateCompany']);

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