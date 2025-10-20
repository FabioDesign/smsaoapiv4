<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{
    ListsController,
    PasswordController,
    RegisterController,
    UserController,
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

//404
Route::fallback(function() {
  $response = [
    'status' => 404,
    'message' => "Page introuvable.",
    'data' => [],
  ];
  return response()->json($response, 404);
});
// Route pour l'inscription
Route::post('register/sendotp', [RegisterController::class, 'sendotp']);
Route::post('register/validotp', [RegisterController::class, 'validotp']);
Route::post('register/forms', [RegisterController::class, 'store']);
// Route pour la connexion
Route::post('users/auth', [UserController::class, 'login']);
// Routes pour les mots de passe oubliés
Route::post('password/verifemail', [PasswordController::class, 'verifemail']);
Route::post('password/verifotp', [PasswordController::class, 'verifotp']);
Route::post('password/addpass', [PasswordController::class, 'addpass']);
// Route pour les listes
Route::get('towns/list/{lg}', [ListsController::class, 'towns']);
Route::get('accountyp/list/{lg}', [ListsController::class, 'accountyp']);

Route::middleware(['auth:api'])->group(function () {
  Route::resources([
    'users' => UserController::class,
  ]);
  // Route pour la modification du profil utilisateur
  Route::post('users/profil', [UserController::class, 'profil']);
  // Route pour la deconnexion
  Route::post('users/logout', [UserController::class, 'logout']);
  // Route pour les mots de passe
  Route::post('password/editpass', [PasswordController::class, 'editpass']);
});
