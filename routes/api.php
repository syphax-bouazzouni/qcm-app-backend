<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\PasswordUpdateController;
use App\Http\Controllers\VerificationApiController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('api')->group(function(){
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/login', [AuthController::class, 'notLogged'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/reset-password', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
        Route::post('/update-password', [PasswordUpdateController::class, 'passwordUpdate']);
        Route::get('/user', [AuthController::class, 'userProfile']);
        Route::post('/captcha', [CaptchaController::class, 'validateCaptcha']);
        Route::get('email/verify/{id}', [VerificationApiController::class,'verify'])->name('verification.verify');
        Route::get('email/resend', [VerificationApiController::class,'resend'])->name('verification.resend');
        Route::get('social/{provider}', [AuthController::class,'validateSocialToken']);
    });
    Route::post('modules/quizzes' ,[\App\Http\Controllers\QuizController::class , 'indexModule']);
    Route::apiResource('modules' , \App\Http\Controllers\ModuleController::class);
    Route::apiResource('quizzes' , \App\Http\Controllers\QuizController::class);
    Route::apiResource('offers' , \App\Http\Controllers\OfferController::class);
    Route::post('images' , [ImageController::class , 'store']);
    Route::get('images/{image}' , [ImageController::class , 'show']);

    Route::get('years/modules' ,[\App\Http\Controllers\YearsModulesController::class , 'index']);
});

