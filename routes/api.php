<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{
    UserController,
    PasswordController,
};

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
// Route pour l'inscription
Route::post('register', [UserController::class, 'store']);
// Route pour la connexion
Route::post('users/auth', [UserController::class, 'login']);
// Routes pour les mots de passe oubliés
Route::post('password/verifemail', [PasswordController::class, 'step1']);
Route::post('password/verifotp', [PasswordController::class, 'step2']);
Route::post('password/addpass', [PasswordController::class, 'step3']);

Route::middleware('auth:api')->group( function () {
    // Route pour les mots de passe
    Route::post('password/editpass', [PasswordController::class, 'editpass']);
    // Route pour la deconnexion
    Route::post('users/logout', [UserController::class, 'logout']);
});
