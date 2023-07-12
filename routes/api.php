<?php

use App\Http\Controllers\AdminAuthRegisterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\subscribeController;
use App\Models\Statement;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

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

    Route::post('statements', [StatementController::class, 'upload']);
    Route::post('createArticle', [ArticleController::class, 'create']);
    Route::put('protest/{type}/{id}', [ArticleController::class, 'editProtest']);
    Route::delete('protest/{type}/{id}', [ArticleController::class, 'deleteProtest']);
    Route::delete('message/{articleType}/{articleId}/{msgId}', [MessageController::class, 'deleteMessage']);
    Route::delete('statements/{id}', [StatementController::class, 'deleteStatement']);
    Route::post('admin/register', AdminAuthRegisterController::class);
    Route::post('medias', [MediaController::class, 'create']);
    Route::delete('medias/{id}', [MediaController::class, 'deleteMedia']);
    Route::post('admin/logout/{id}', [AuthController::class, 'logout']);
});
Route::get('resizeImage', [ArticleController::class, 'resizeImage']);
Route::get('medias', [MediaController::class, 'getMedia']);
Route::post('login', [AuthController::class, 'login']);
Route::get('statements/', [StatementController::class, 'getStatement']);
Route::get('statementJoint/', [StatementController::class, 'getJointStatement']);
Route::get('statement/{id}', [StatementController::class, 'getOneStatement']);
Route::get('protest/{type}', [ArticleController::class, 'showProtest']);
Route::get('protest/{type}/{committees}/{id}', [ArticleController::class, 'showThumbnails']);
Route::post('message/{type}/{id}', [MessageController::class, 'saveMessage']);
Route::get('message/{type}/{id}', [MessageController::class, 'getMessage']);
Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::post('subscribe', [subscribeController::class, 'store']);
