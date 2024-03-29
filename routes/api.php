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
        Route::get('/refresh', [AuthController::class, 'refresh']);
        Route::post('/reset-password', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
        Route::post('/update-password', [PasswordUpdateController::class, 'passwordUpdate']);
        Route::get('/user', [AuthController::class, 'userProfile']);
        Route::post('/captcha', [CaptchaController::class, 'validateCaptcha']);
        Route::get('email/verify/{id}', [VerificationApiController::class,'verify'])->name('verification.verify');
        Route::post('email/resendmail', [VerificationApiController::class,'resend'])->name('verification.resend');
        Route::get('social/{provider}', [AuthController::class,'validateSocialToken']);
    });
    Route::get('/users', [\App\Http\Controllers\UsersController::class, 'index']);
    Route::post('/users/{id}', [\App\Http\Controllers\UsersController::class, 'update']);
    Route::post('modules/quizzes' ,[\App\Http\Controllers\QuizController::class , 'index']);
    Route::post('module/quizzes/filter' ,[\App\Http\Controllers\ModuleController::class , 'moduleWithQuizzes']);
    Route::post('user/favs' ,[\App\Http\Controllers\FavsController::class , 'index']);
    Route::post('user/favs/addtests' ,[\App\Http\Controllers\FavsController::class , 'addTests']);
    Route::post('user/favs/removetest' ,[\App\Http\Controllers\FavsController::class , 'removeTest']);
    Route::get('user/favs/tests/{fav}' ,[\App\Http\Controllers\FavsController::class , 'indexOfFav']);
    Route::get('reports/count' , [\App\Http\Controllers\ReportsController::class, 'count']);
    Route::get('notifications/count' , [\App\Http\Controllers\NotificationController::class, 'count']);
    Route::apiResource('years' , \App\Http\Controllers\YearsController::class);
    Route::get('year/infos' , [\App\Http\Controllers\YearsController::class , 'infos']);
    Route::apiResource('modules' , \App\Http\Controllers\ModuleController::class);
    Route::apiResource('quizzes' , \App\Http\Controllers\QuizController::class);
    Route::post('quizzes/orders' , [\App\Http\Controllers\QuizController::class , 'updateOrder']);
    Route::apiResource('favs' , \App\Http\Controllers\FavsController::class);
    Route::apiResource('reports' , \App\Http\Controllers\ReportsController::class);
    Route::apiResource('notifications' , \App\Http\Controllers\NotificationController::class);
    Route::apiResource('offers' , \App\Http\Controllers\OfferController::class);
    Route::post('tests/filter' , [\App\Http\Controllers\TestController::class , 'index']);
    Route::apiResource('tests' , \App\Http\Controllers\TestController::class);
    Route::apiResource('questions' , \App\Http\Controllers\QuestionController::class);
    Route::post('questions/filter' , [\App\Http\Controllers\QuestionController::class , 'index']);

    Route::post('images' , [ImageController::class , 'store']);
    Route::get('images/{image}' , [ImageController::class , 'show']);


    Route::post('quiz/session/start' ,[\App\Http\Controllers\QuizSessionController::class , 'startSession']);
    Route::post('quiz/session/new' ,[\App\Http\Controllers\QuizSessionController::class , 'startNewSession']);
    Route::post('quiz/session/save' ,[\App\Http\Controllers\QuizSessionController::class , 'saveSession']);
    Route::post('quiz/session/restart' ,[\App\Http\Controllers\QuizSessionController::class , 'restartSession']);
    Route::get('quiz/session' ,[\App\Http\Controllers\QuizSessionController::class , 'index']);
    Route::delete('quiz/session/{id}' ,[\App\Http\Controllers\QuizSessionController::class , 'destroy']);
    Route::get('quiz/session/{id}' ,[\App\Http\Controllers\QuizSessionController::class , 'show']);
});

