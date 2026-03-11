<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\QuestionController;
use App\Http\Controllers\User\ResponseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::get('/questions/{question}', [QuestionController::class, 'show']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    Route::get('/responses', [ResponseController::class, 'index']);
    Route::post('/questions/{question}/responses', [ResponseController::class, 'store']);
    Route::delete('/responses/{response}', [ResponseController::class, 'destroy']);
});
