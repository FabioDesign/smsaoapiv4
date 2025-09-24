<?php

namespace App\Http\Controllers\API;

use \Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\{Permission, Profile, User};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{App, DB, Validator, Log, Auth};
use App\Http\Controllers\API\BaseController as BaseController;

class ProfileController extends BaseController
{
    //Liste des profils
    /**
    * @OA\Get(
    *   path="/api/profiles",
    *   tags={"Profiles"},
    *   operationId="listProfile",
    *   description="Liste des profils",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Liste des profils."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function index(): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        try {
            // Code to list profiles
            $query = Profile::select('uid', $user->lg . ' as label', 'description_' . $user->lg . ' as description', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get();
            // Vérifier si les données existent
            if ($query->isEmpty()) {
                Log::warning("Profile::index - Aucun profil trouvé.");
                return $this->sendError("Aucune donnée trouvée.", [], 404);
            }
            // Transformer les données
            $data = $query->map(fn($data) => [
                'uid' => $data->uid,
                'label' => $data->label,
                'description' => $data->description,
                'status' => $data->status ? 'Activé':'Désactivé',
                'date' => Carbon::parse($data->created_at)->format('d/m/Y H:i'),
            ]);
            return $this->sendSuccess("Liste des profils récupérée avec succès.", $data);
        } catch (\Exception $e) {
            Log::warning("Profile::index - Erreur lors de la récupération des profils: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des profils.");
        }
    }
    //Détail d'un profil
    /**
    * @OA\Get(
    *   path="/api/profiles/{uid}",
    *   tags={"Profiles"},
    *   operationId="showProfile",
    *   description="Détail d'un profil",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Détail d'un profil."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function show($uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        // Vérifier si l'ID est présent et valide
        $profile = Profile::select('id', $user->lg . ' as label', 'description_' . $user->lg . ' as description', 'status')
        ->where('uid', $uid)
        ->first();
        if (!$profile) {
            Log::warning("Profile::show - Aucun profil trouvé pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        try {
            // Charger les permissions avec eager loading et les transformer directement
            $permissions = $profile->permissions
            ->sortBy(['menu_id', 'action_id'])
            ->map(function ($permission) {
                return "{$permission->menu_id}|{$permission->action_id}";
            })
            ->values()
            ->all();
            // Retourner les détails du profil avec les permissions
            return $this->sendSuccess('Détails sur le profil', [
                'label' => $profile->label,
                'description' => $profile->description,
                'status' => match((int)$profile->status) {
                    0 => 'Désactivé',
                    1 => 'Activé'
                },
                'permissions' => $permissions,
            ]);
        } catch(\Exception $e) {
            Log::warning("Profile::show - Erreur d'affichage d'un profil : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage d'un profil");
        }
    }
    //Enregistrement
    /**
    * @OA\Post(
    *   path="/api/profiles",
    *   tags={"Profiles"},
    *   operationId="storeProfile",
    *   description="Enregistrement d'un profil",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"en", "fr", "permissions"},
    *         @OA\Property(property="en", type="string", example="Dashboard"),
    *         @OA\Property(property="fr", type="string", example="Tableau de bord"),
    *         @OA\Property(property="description_en", type="text", example="Dashboard"),
    *         @OA\Property(property="description_fr", type="text", example="Tableau de bord"),
    *         @OA\Property(property="permissions", type="array", @OA\Items(
    *               @OA\Property(property="menu_id", type="integer"),
    *               @OA\Property(property="action_id", type="integer"),
    *               example="[1|2, 3|4]"
    *           )
    *         ),
    *      )
    *   ),
    *   @OA\Response(response=200, description="Profil enregisté avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function store(Request $request): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Profile::store - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'en' => 'required|string|max:255|unique:profiles,en',
            'fr' => 'required|string|max:255|unique:profiles,fr',
            'description_en' => 'present',
            'description_fr' => 'present',
            'permissions' => 'required|array',
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Profile::store - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Création de la reclamation
        $set = [
            'status' => 1,
            'en' => $request->en,
            'fr' => $request->fr,
            'created_user' => $user->id,
            'description_en' => $request->description_en ?? '',
            'description_fr' => $request->description_fr ?? '',
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            $profil = Profile::create($set);
            // Valider la transaction
            DB::commit();
            // Si des permissions sont fournies, les associer au profil
            if ($request->has('permissions') && is_array($request->permissions)) {
                foreach ($request->permissions as $permissions) {
                    $permission = Str::of($permissions)->explode('|');
                    // Enregistrer la permission
                    Permission::create([
                        'menu_id' => $permission[0],
                        'action_id' => $permission[1],
                        'profile_id' => $profil->id,
                    ]);
                }
            }
            return $this->sendSuccess("Profil enregistré avec succès.", [
                'en' => $request->en,
                'fr' => $request->fr,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("Profile::store : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de l'enregistrement du Profil.");
        }
    }
    // Modification
    /**
    * @OA\Put(
    *   path="/api/profiles/{uid}",
    *   tags={"Profiles"},
    *   operationId="editProfile",
    *   description="Modification d'un profil",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"en", "fr", "permissions", "status"},
    *         @OA\Property(property="en", type="string", example="Dashboard"),
    *         @OA\Property(property="fr", type="string", example="Tableau de bord"),
    *         @OA\Property(property="description_en", type="text", example="Dashboard"),
    *         @OA\Property(property="description_fr", type="text", example="Tableau de bord"),
    *         @OA\Property(property="status", type="integer", example=1),
    *         @OA\Property(property="permissions", type="array", @OA\Items(
    *               @OA\Property(property="menu_id", type="integer"),
    *               @OA\Property(property="action_id", type="integer"),
    *               example="[1|2, 3|4]"
    *           )
    *         )
    *      )
    *   ),
    *   @OA\Response(response=200, description="Profil modifié avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function update(request $request, $uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Profile::update - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'en' => 'required|string|max:255|unique:profiles,en,' . $uid,
            'fr' => 'required|string|max:255|unique:profiles,fr,' . $uid,
            'description_en' => 'present',
            'description_fr' => 'present',
            'status' => 'required|integer|in:0,1',
            'permissions' => 'required|array',
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Profile::update - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Vérifier si l'ID est présent et valide
        $query = Profile::where('uid', $uid)->first();
        if (!$query) {
            Log::warning("Profile::update - Aucun profil trouvé pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        // Création de la reclamation
        $set = [
            'en' => $request->en,
            'fr' => $request->fr,
            'updated_user' => $user->id,
            'status' => $request->status,
            'description_en' => $request->description_en ?? '',
            'description_fr' => $request->description_fr ?? '',
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            $query->update($set);
            // Valider la transaction
            DB::commit();
            // Si des permissions sont fournies, les associer au profil
            if ($request->has('permissions') && is_array($request->permissions)) {
                // Supprimer les permissions existantes pour ce profil
                Permission::where('profile_id', $query->id)->delete();
                // Parcourir les permissions fournies
                foreach ($request->permissions as $permissions) {
                    $permission = Str::of($permissions)->explode('|');
                    // Enregistrer la permission
                    Permission::create([
                        'menu_id' => $permission[0],
                        'action_id' => $permission[1],
                        'profile_id' => $query->id,
                    ]);
                }
            }
            return $this->sendSuccess("Profil modifié avec succès.", [
                'en' => $request->en,
                'fr' => $request->fr,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("Profile::update : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de l'enregistrement du Profil.");
        }
	}
    // Suppression d'un profil
    /**
    *   @OA\Delete(
    *   path="/api/profiles/{uid}",
    *   tags={"Profiles"},
    *   operationId="deleteProfile",
    *   description="Suppression d'un profil",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Menu supprimé avec succès."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function destroy($id): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Profile::destroy - ID User : {$user->id} - Requête : " . $id);
        try {
            // Vérification des dépendances en utilisant exists() pour une meilleure performance
            if (User::where('profile_id', $id)->exists()) {
                Log::warning("Profile::destroy - Tentative de suppression d'un profil avec des permissions associées : " . $id);
                return $this->sendError("Impossible de supprimer le profil.", [], 403);
            }
            //Suppression
            $deleted = Profile::destroy($id);
            if (!$deleted) {
                Log::warning("Profile::destroy - Tentative de suppression d'un profil inexistante : " . $id);
                return $this->sendError("Profile introuvable.", [], 403);
            }
            Permission::where('profile_id', $id)->delete();
            return $this->sendSuccess("Profile supprimé avec succès.");
        } catch(\Exception $e) {
            Log::warning("Profile::destroy - Erreur lors de la suppression d'un profil : " . $e->getMessage());
            return $this->sendError("Erreur lors de la suppression d'un profil.");
        }
    }
}
