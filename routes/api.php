<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoyerController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\TodoTacheController;
use App\Http\Controllers\AddUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoriqueController;
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
Route::fallback(function(){
    return view('404');
});
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/essais', [AuthController::class, 'essais']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function() {

    // user
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    //Obtenir tous les utilisateurs qui ne sont pas encore dans un foyer
    Route::get('/allUser', [UserController::class, 'allUser']);
    //Obtenir tous les utilisateurs qui sont dans le foyer
    Route::post('/foyer/{id}/allMembre', [UserController::class, 'allMembre']);
    //Activer ou désactiver un utilisateur
    Route::post('/active', [UserController::class, 'active']);
    //Changer l'admin du foyer
    Route::post('/changeAdmin', [UserController::class, 'changeAdmin']);
    Route::post('/updateUserPreference', [UserController::class, 'updateUserPreference']);
    Route::post('/updateUser', [UserController::class, 'updateUser']);

    //Grouper les utilisateurs
    Route::post('/createGroupe', [AddUserController::class, 'createGroupe']);
    Route::get('/getListGroupe', [AddUserController::class, 'getListGroupe']);
    Route::delete('/removeFromGroupe', [AddUserController::class, 'removeFromGroupe']);
    Route::delete('/deleteGroupe', [AddUserController::class, 'deleteGroupe']);
    

    //Foyer
    Route::get('/foyer', [FoyerController::class, 'index']);
    Route::post('/foyer', [FoyerController::class, 'store']);
    Route::put('/foyer/{id}', [FoyerController::class, 'update']);
    Route::delete('/foyer/{id}', [FoyerController::class, 'delete']);


    //Tache
    Route::get('/foyer/{id}/tache', [TacheController::class, 'index']);
    Route::post('/foyer/{id}/tache', [TacheController::class, 'store']);
    Route::put('/tache/{id}', [TacheController::class, 'update']);
    Route::delete('/deleteTache', [TacheController::class, 'delete']);


    //Tache à faire
    Route::post('/foyer/{id}/todoTache', [TodoTacheController::class, 'todoTache']);

    //Ajouter un utilisateur dans un foyer
    Route::post('/foyer/{id}/addUser', [AddUserController::class, 'addUser']);
    Route::delete('/removeUser', [AddUserController::class, 'removeUser']);

    //Marquer comme fini une tache
    Route::post('/historique', [HistoriqueController::class, 'historique']);
    Route::get('/historique', [HistoriqueController::class, 'Listconfirmation']);
    Route::post('/confirmer', [HistoriqueController::class, 'confirmer']);

});