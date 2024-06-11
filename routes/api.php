<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoyerController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\TodoTacheController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function() {

    // user
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);


    //Foyer
    Route::get('/foyer', [FoyerController::class, 'index']);
    Route::post('/foyer', [FoyerController::class, 'store']);
    Route::put('/foyer/{id}', [FoyerController::class, 'update']);
    Route::delete('/foyer/{id}', [FoyerController::class, 'delete']);


    //Tache
    Route::get('/foyer/{id}/tache', [TacheController::class, 'index']);
    Route::post('/foyer/{id}/tache', [TacheController::class, 'store']);
    Route::put('/tache/{id}', [TacheController::class, 'update']);
    Route::delete('/tache/{id}', [TacheController::class, 'delete']);


    //Tache à faire
    Route::post('/foyer/{id}/todoTache', [TodoTacheController::class, 'todoTache']);
});