<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\CountryController;

Route::middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/users', UserController::class);
        Route::apiResource('/customers', CustomerController::class);
        
        Route::get('/orders', [ApiOrderController::class, 'index']);
        Route::get('/orders/statuses', [ApiOrderController::class, 'getStatuses']);
        Route::post('/orders/update-status/{order}/{status}', [ApiOrderController::class, 'updateStatus']);
        Route::get('/orders/{order}', [ApiOrderController::class, 'view']);

        Route::get('/dashboard/active-customers', [DashboardController::class, 'activeCustomers']);
        Route::get('/dashboard/active-products', [DashboardController::class, 'activeProducts']);
        Route::get('/dashboard/paid-orders', [DashboardController::class, 'paidOrders']);
        Route::get('/dashboard/total-sales', [DashboardController::class, 'totalSales']);
        Route::get('/dashboard/orders-by-state', [DashboardController::class, 'ordersByState']);
        
    });

Route::post('/login', [AuthController::class, 'login']);

Route::get('/countries/{countryCode}/states', [CountryController::class, 'states']);