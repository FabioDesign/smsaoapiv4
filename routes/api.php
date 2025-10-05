<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{
    DocumentController,
    ListsController,
    PasswordController,
    ProfileController,
    RegisterController,
    RequestdocController,
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
Route::get('menus/list/{lg}', [ListsController::class, 'menus']);
Route::get('actions/list/{lg}', [ListsController::class, 'actions']);
Route::get('periods/list/{lg}', [ListsController::class, 'periods']);
Route::get('country/list/{lg}', [ListsController::class, 'country']);
Route::get('provinces/list/{lg}', [ListsController::class, 'provinces']);
Route::get('cells/list/{lg}/{sector_id}', [ListsController::class, 'cells']);
Route::get('nationality/list/{lg}', [ListsController::class, 'nationality']);
Route::get('regions/list/{lg}/{country_id}', [ListsController::class, 'regions']);
Route::get('sectors/list/{lg}/{district_id}', [ListsController::class, 'sectors']);
Route::get('districts/list/{lg}/{province_id}', [ListsController::class, 'districts']);

Route::middleware(['auth:api'])->group(function () {
  Route::resources([
    'users' => UserController::class,
    'profiles' => ProfileController::class,
    'documents' => DocumentController::class,
    'requestdoc' => RequestdocController::class,
  ]);
  // Route pour la modification du profil utilisateur
  Route::post('users/profil', [UserController::class, 'profil']);
  // Route pour la photo de profil
  Route::post('users/photo', [UserController::class, 'photo']);
  // Route pour la deconnexion
  Route::post('users/logout', [UserController::class, 'logout']);
  // Route pour les mots de passe
  Route::post('password/editpass', [PasswordController::class, 'editpass']);
});
