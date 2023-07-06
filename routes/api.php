<?php

use App\Http\Controllers\AdminAuthRegisterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\subscribeController;
use App\Models\Statement;
use Illuminate\Auth\Events\Verified;

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

Route::middleware(['auth:sanctum', ])->group(function () {

    Route::post('statement', [StatementController::class, 'upload']);
    Route::post('createArticle', [ArticleController::class, 'create']);
    Route::put('protest/{type}/{id}', [ArticleController::class, 'editProtest']);
    Route::delete('protest/{type}/{id}', [ArticleController::class, 'deleteProtest']);
    Route::delete('message/{articleType}/{articleId}/{msgId}', [MessageController::class, 'deleteMessage']);
    Route::delete('statement/{id}', [StatementController::class, 'deleteStatement']);
    Route::post('admin/register', AdminAuthRegisterController::class);
});

Route::post('login', [AuthController::class, 'login']);
Route::get('statement/{page?}', [StatementController::class, 'getStatement']);
Route::get('statementJoint/{page?}', [StatementController::class, 'getJointStatement']);
Route::get('protest/{type}', [ArticleController::class, 'showProtest']);
Route::get('protest/{type}/{id}', [ArticleController::class, 'showThumbnails']);
Route::post('message/{type}/{id}', [MessageController::class, 'saveMessage']);
Route::get('message/{type}/{id}', [MessageController::class, 'getMessage']);
Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::post('subscribe', [subscribeController::class, 'store']);
