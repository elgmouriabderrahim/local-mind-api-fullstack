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

Route::middleware('guest:sanctum')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::get('/questions/{question}', [QuestionController::class, 'show'])->whereNumber('question');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->whereNumber('question');
    Route::patch('/questions/{question}', [QuestionController::class, 'update'])->whereNumber('question');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->whereNumber('question');

    Route::get('/responses', [ResponseController::class, 'index']);
    Route::post('/questions/{question}/responses', [ResponseController::class, 'store'])->whereNumber('question');
    Route::put('/responses/{response}', [ResponseController::class, 'update'])->whereNumber('response');
    Route::patch('/responses/{response}', [ResponseController::class, 'update'])->whereNumber('response');
    Route::delete('/responses/{response}', [ResponseController::class, 'destroy'])->whereNumber('response');

    Route::get('/favourites', [FavouriteController::class, 'index']);
    Route::post('/questions/{question}/favourite', [FavouriteController::class, 'toggle'])->whereNumber('question');
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/users', [AdminUserController::class, 'index']);

    Route::prefix('questions')->group(function () {
        Route::get('/', [AdminQuestionController::class, 'index']);
        Route::get('/{question}', [AdminQuestionController::class, 'show'])->whereNumber('question');
        Route::delete('/{question}', [AdminQuestionController::class, 'destroy'])->whereNumber('question');
    });
});
