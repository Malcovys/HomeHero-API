<?php

use App\Http\Controllers\Api\HouseController;
use App\Http\Controllers\Api\HouseMemeberController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserTaskController;
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

    // Houseless users
    Route::get('/users/houseless/search', [UserController::class, 'searchHouselessUser']);

    // Houses
    Route::prefix('/house')->group(function() {
        Route::get('/', [HouseController::class, 'getAll']);
        Route::post('/', [HouseController::class, 'create']);

        // Memeber
        Route::prefix('/member')->group(function () {
            Route::get('/', [HouseMemeberController::class, 'getHouseMate']);
            Route::post('/add/{user_id}', [HouseMemeberController::class, 'add']);
        });

        // Tasks
        Route::prefix('/task')->group(function(): void{
            Route::get('/', [TaskController::class, 'getHouseTasks']);
            Route::post('/', [TaskController::class, 'create']);

            // Board
            Route::get('/bard', [TaskController::class, 'assing']);
        });
    });
});