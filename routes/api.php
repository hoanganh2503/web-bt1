<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\DeliveryController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\UserController;
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

Route::group(['prefix'=>'admin'], function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-profile', [AuthController::class, 'changeProfile']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::get('/user', [AuthController::class, 'user']);

        Route::group(['prefix'=>'categories'], function(){
            Route::get('/index', [CategoryController::class, 'index']);
            Route::get('/detail', [CategoryController::class, 'detail']);
            Route::post('/create', [CategoryController::class, 'create']);
            Route::post('/edit', [CategoryController::class, 'edit']);
            Route::delete('/delete', [CategoryController::class, 'delete']);
        });

        Route::group(['prefix'=>'deliveries'], function(){
            Route::get('/index', [DeliveryController::class, 'index']);
            Route::get('/detail', [DeliveryController::class, 'detail']);
            Route::post('/create', [DeliveryController::class, 'create']);
            Route::post('/edit', [DeliveryController::class, 'edit']);
            Route::delete('/delete', [DeliveryController::class, 'delete']);
        });

        Route::group(['prefix'=>'users'], function(){
            Route::get('/index', [UserController::class, 'index']);
            Route::get('/detail', [UserController::class, 'detail']);
            Route::post('/change-status', [UserController::class, 'changeStatus']);
            Route::delete('/delete', [UserController::class, 'delete']);

        });

        Route::group(['prefix'=>'products'], function(){
            Route::get('/index', [ProductController::class, 'index']);
            Route::get('/detail', [ProductController::class, 'detail']);
            Route::post('/create', [ProductController::class, 'create']);
            Route::post('/edit', [ProductController::class, 'edit']);
            Route::delete('/delete', [ProductController::class, 'delete']);

            Route::get('/detail-child', [ProductController::class, 'detailChild']);
            Route::post('/create-child', [ProductController::class, 'createChild']);
            Route::post('/edit-child', [ProductController::class, 'editChild']);
            Route::delete('/delete-child', [ProductController::class, 'deleteChild']);
        });

    });    
});


