<?php

namespace App\Http\Controllers\API;

use \Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\{File, Requestdoc};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{App, DB, Validator, Log, Auth};
use App\Http\Controllers\API\BaseController as BaseController;

class RequestdocController extends BaseController
{
    //Liste des pièces jointes
    /**
    * @OA\Get(
    *   path="/api/requestdoc",
    *   tags={"Requestdoc"},
    *   operationId="listRequestdoc",
    *   description="Liste des pièces jointes",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Liste des pièces jointes."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function index(): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        try {
            // Code to list Requestdoc
            $query = Requestdoc::select('uid', $user->lg . ' as label', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get();
            // Vérifier si les données existent
            if ($query->isEmpty()) {
                Log::warning("Requestdoc::index - Aucune pièce jointe trouvée.");
                return $this->sendError("Aucune donnée trouvée.", [], 404);
            }
            // Transformer les données
            $data = $query->map(fn($data) => [
                'uid' => $data->uid,
                'label' => $data->label,
                'status' => $data->status ? 'Activé':'Désactivé',
                'date' => Carbon::parse($data->created_at)->format('d/m/Y H:i'),
            ]);
            return $this->sendSuccess("Liste des pièces jointes récupérée avec succès.", $data);
        } catch (\Exception $e) {
            Log::warning("Requestdoc::index - Erreur lors de la récupération des pièces jointes: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des pièces jointes.");
        }
    }
    //Détail d'une pièce jointe
    /**
    * @OA\Get(
    *   path="/api/requestdoc/{uid}",
    *   tags={"Requestdoc"},
    *   operationId="showRequestdoc",
    *   description="Détail d'une pièce jointe",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Détail d'une pièce jointe."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function show($uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        // Vérifier si l'ID est présent et valide
        $requestdoc = Requestdoc::select($user->lg . ' as label', 'status')
        ->where('uid', $uid)
        ->first();
        if (!$requestdoc) {
            Log::warning("Requestdoc::show - Aucune pièce jointe trouvée pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        try {
            // Retourner les détails d'une pièce jointe
            return $this->sendSuccess('Détails sur la pièce jointe', [
                'label' => $requestdoc->label,
                'status' => $requestdoc->status ? 'Activé':'Désactivé',
            ]);
        } catch(\Exception $e) {
            Log::warning("Requestdoc::show - Erreur d'affichage d'une pièce jointe : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage d'une pièce jointe");
        }
    }
    //Enregistrement
    /**
    * @OA\Post(
    *   path="/api/requestdoc",
    *   tags={"Requestdoc"},
    *   operationId="storeRequestdoc",
    *   description="Enregistrement d'une pièce jointe",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"en", "fr"},
    *         @OA\Property(property="en", type="string"),
    *         @OA\Property(property="fr", type="string"),
    *      )
    *   ),
    *   @OA\Response(response=200, description="Pièce jointe enregistée avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function store(Request $request): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Requestdoc::store - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'en' => 'required|string|max:255|unique:requestdoc,en',
            'fr' => 'required|string|max:255|unique:requestdoc,fr',
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Requestdoc::store - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Création de la reclamation
        $set = [
            'status' => 1,
            'en' => $request->en,
            'fr' => $request->fr,
            'created_user' => $user->id,
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            Requestdoc::create($set);
            // Valider la transaction
            DB::commit();
            return $this->sendSuccess("Pièce jointe enregistré avec succès.", [
                'en' => $request->en,
                'fr' => $request->fr,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("Requestdoc::store : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de l'enregistrement de pièce jointe.");
        }
    }
    // Modification
    /**
    * @OA\Put(
    *   path="/api/requestdoc/{uid}",
    *   tags={"Requestdoc"},
    *   operationId="editRequestdoc",
    *   description="Modification d'une pièce jointe",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"en", "fr", "status"},
    *         @OA\Property(property="en", type="string"),
    *         @OA\Property(property="fr", type="string"),
    *         @OA\Property(property="status", type="integer"),
    *      )
    *   ),
    *   @OA\Response(response=200, description="Pièce jointe modifiée avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function update(request $request, $uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Requestdoc::update - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'en' => 'required|string|max:255|unique:requestdoc,en,' . $uid,
            'fr' => 'required|string|max:255|unique:requestdoc,fr,' . $uid,
            'status' => 'required|integer|in:0,1',
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Requestdoc::update - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Vérifier si l'ID est présent et valide
        $query = Requestdoc::where('uid', $uid)->first();
        if (!$query) {
            Log::warning("Requestdoc::update - Aucune pièce jointe trouvée pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        // Création de la reclamation
        $set = [
            'en' => $request->en,
            'fr' => $request->fr,
            'updated_user' => $user->id,
            'status' => $request->status,
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            $query->update($set);
            // Valider la transaction
            DB::commit();
            return $this->sendSuccess("Pièce jointe modifié avec succès.", [
                'en' => $request->en,
                'fr' => $request->fr,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("Requestdoc::update : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de modification d'une Pièce jointe.");
        }
	}
    // Suppression d'une pièce jointe
    /**
    *   @OA\Delete(
    *   path="/api/requestdoc/{uid}",
    *   tags={"Requestdoc"},
    *   operationId="deleteRequestdoc",
    *   description="Suppression d'une pièce jointe",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Pièce jointe supprimée avec succès."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function destroy($uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Requestdoc::destroy - ID User : {$user->id} - Requête : " . $uid);
        try {
            // Vérification si la pièce jointe est attribué à un document
            $requestdoc = Requestdoc::select('requestdocs.id', 'requestdoc_id')
            ->where('requestdocs.uid', $uid)
            ->leftJoin('files', 'files.requestdoc_id','=','requestdocs.id')
            ->first();
            if ($requestdoc->requestdoc_id != null) {
                Log::warning("Requestdoc::destroy - Tentative de suppression d'une pièce jointe déjà attribuée à un document : " . $uid);
                return $this->sendError("Pièce jointe est déjà attribuée à un document.", [], 403);
            }
            // Suppression
            $deleted = Requestdoc::destroy($requestdoc->id);
            if (!$deleted) {
                Log::warning("Requestdoc::destroy - Tentative de suppression d'une pièce jointe inexistante : " . $uid);
                return $this->sendError("Impossible de supprimer la pièce jointe.", [], 403);
            }
            File::where('requestdoc_id', $requestdoc->id)->delete();
            return $this->sendSuccess("Pièce jointe supprimée avec succès.");
        } catch(\Exception $e) {
            Log::warning("Requestdoc::destroy - Erreur lors de la suppression d'une pièce jointe : " . $e->getMessage());
            return $this->sendError("Erreur lors de la suppression d'une pièce jointe.");
        }
    }
}
