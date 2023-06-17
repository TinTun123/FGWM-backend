<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('statement', [StatementController::class, 'upload']);
    Route::post('createArticle', [ArticleController::class, 'create']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('statement', [StatementController::class, 'upload']);
Route::get('statement/{page?}', [StatementController::class, 'getStatement']);