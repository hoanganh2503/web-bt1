<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BillController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\DeliveryController;
use App\Http\Controllers\api\HomeController;
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
// API for admin
Route::group(['prefix'=>'admin'], function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function() {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/change-profile', [AuthController::class, 'changeProfile']);
        Route::get('/profile', [AuthController::class, 'profile']);

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

        Route::group(['prefix'=>'bills'], function(){
            Route::get('/index', [BillController::class, 'index']);
            Route::get('/detail', [BillController::class, 'detail']);
            Route::post('/change-status', [BillController::class, 'changeStatus']);
        });

    });    
});


// API for user
Route::post('/login', [AuthController::class, 'userLogin']);
Route::post('/register', [AuthController::class, 'userRegister']);
Route::get('/home', [HomeController::class, 'home']);
Route::get('/product', [HomeController::class, 'product']);

Route::middleware(['auth:sanctum', 'user'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logoutUser']);
    Route::post('/add-to-cart', [HomeController::class, 'addToCart']);
    Route::get('/cart', [HomeController::class, 'cart']);
    Route::get('/profile', [HomeController::class, 'profile']);
    Route::post('/change-profile', [HomeController::class, 'changeProfile']);

    Route::get('/address', [HomeController::class, 'address']);
    Route::get('/detail-address', [HomeController::class, 'detailAddress']);
    Route::post('/create-address', [HomeController::class, 'createAddress']);
    Route::post('/edit-address', [HomeController::class, 'editAddress']);
    Route::delete('/delete-address', [HomeController::class, 'deleteAddress']);

    Route::get('/checkout', [HomeController::class, 'checkout']);
    Route::post('/order', [HomeController::class, 'order']);
    Route::get('/order-history', [HomeController::class, 'orderHistory']);
    Route::get('/order-detail', [HomeController::class, 'orderDetail']);
    Route::get('/change-status', [HomeController::class, 'changeStatus']);
});
