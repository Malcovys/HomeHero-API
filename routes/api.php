<?php

use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\HouseMemeberController;
use App\Http\Controllers\Api\UserController;
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

// Public routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function() {

    // Houses
    Route::prefix('/house')->group(function() {
        Route::post('/', [HouseController::class, 'create']);
        Route::post('/', [HouseController::class, 'getAll']);

        // Memeber
        Route::prefix('/member')->group(function () {
            Route::get('/', [HouseMemeberController::class, 'getAll']);
            Route::post('/add/{user_id}', [HouseMemeberController::class, 'add']);
        });
        
    });
});