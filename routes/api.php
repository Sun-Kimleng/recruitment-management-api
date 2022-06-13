<?php

use App\Http\Controllers\ForgetpasswordController;
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
Route::middleware(['auth:sanctum', 'verified'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        Route::post('/logout',[UserController::class, 'logout'])->name('logout');
        Route::get('/checkAuth', function(){return response()->json(['message'=>'You are authenticated', 'status'=>200]);});
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


