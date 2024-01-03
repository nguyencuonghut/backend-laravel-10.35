<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::post('/users/bulk_destroy', [UsersController::class, 'bulkDestroy']);
    Route::apiResource('/users',UsersController::class);

    Route::get('/user', [UsersController::class, 'getAuthUser']);
    Route::post('/change_password', [UsersController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/reset_password', [AuthController::class, 'resetPassword']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot_password', [AuthController::class, 'forgotPassword']);
