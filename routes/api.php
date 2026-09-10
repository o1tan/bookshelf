<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;

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

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::apiResource('books', BookController::class)
            ->only(['index', 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::apiResource('books', BookController::class)
                ->only(['store', 'update', 'destroy']);
        });
    });
