<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\User\QuestionController;
use App\Http\Controllers\Api\User\ResponseController;
use App\Http\Controllers\Api\User\FavouriteController;
use App\Http\Controllers\Api\User\HomeController;

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminQuestionController;
use App\Http\Controllers\Api\Admin\AdminUserController;

use App\Http\Controllers\Api\AuthController;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('guest:sanctum')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::get('/questions/{question}', [QuestionController::class, 'show']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    Route::get('/responses', [ResponseController::class, 'index']);
    Route::post('/questions/{question}/responses', [ResponseController::class, 'store']);
    Route::delete('/responses/{response}', [ResponseController::class, 'destroy']);
});