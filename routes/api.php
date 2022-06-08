<?php

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

Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix('/user')->name('user.')->group(function(){
        Route::get('checkAuth', function(){return response()->json(['message'=>'You are authenticated', 'status'=>200]);});
        Route::post('/logout',[UserController::class, 'logout'])->name('logout');
    });
});

Route::prefix('/user')->name('user.')->group(function(){
    Route::post('/create', [UserController::class, 'create'])->name('create');
    Route::post('/login',[UserController::class, 'login'])->name('login');
});

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name

Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');


