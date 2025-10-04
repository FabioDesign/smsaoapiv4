<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\{
    DocumentController,
    PasswordController,
    ProfileController,
    RegisterController,
    RequestdocController,
    SettingController,
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
// Route pour les paramètres
Route::get('settings/country/{lg}', [SettingController::class, 'country']);
Route::get('settings/province/{lg}', [SettingController::class, 'province']);
Route::get('settings/town/{lg}/{region_id}', [SettingController::class, 'town']);
Route::get('settings/cells/{lg}/{sector_id}', [SettingController::class, 'cells']);
Route::get('settings/nationality/{lg}', [SettingController::class, 'nationality']);
Route::get('settings/region/{lg}/{country_id}', [SettingController::class, 'region']);
Route::get('settings/sector/{lg}/{district_id}', [SettingController::class, 'sector']);
Route::get('settings/district/{lg}/{province_id}', [SettingController::class, 'district']);

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
  // Route pour les actions
  Route::get('actions/lists', [ProfileController::class, 'actions']);
  // Route pour les menus
  Route::get('menus/lists', [ProfileController::class, 'menus']);
});
