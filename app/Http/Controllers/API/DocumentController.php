<?php

namespace App\Http\Controllers\API;

use \Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\{Document, File};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{App, DB, Validator, Log, Auth};
use App\Http\Controllers\API\BaseController as BaseController;

class DocumentController extends BaseController
{
    //Liste des documents
    /**
    * @OA\Get(
    *   path="/api/documents",
    *   tags={"Documents"},
    *   operationId="listDocs",
    *   description="Liste des documents",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Liste des documents."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function index(): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        try {
            // Code to list documents
            $query = Document::select('uid', 'code', $user->lg . ' as label', 'amount', 'deadline', 'description_' . $user->lg . ' as description', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->get();
            // Vérifier si les données existent
            if ($query->isEmpty()) {
                Log::warning("Document::index - Aucun document trouvé.");
                return $this->sendError("Aucune donnée trouvée.", [], 404);
            }
            // Transformer les données
            $data = $query->map(fn($data) => [
                'uid' => $data->uid,
                'code' => $data->code,
                'label' => $data->label,
                'amount' => $data->amount,
                'deadline' => $data->deadline,
                'description' => $data->description,
                'status' => $data->status ? 'Activé':'Désactivé',
                'date' => Carbon::parse($data->created_at)->format('d/m/Y H:i'),
            ]);
            return $this->sendSuccess("Liste des documents.", $data);
        } catch (\Exception $e) {
            Log::warning("Document::index - Erreur lors de la récupération des documents: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des documents.");
        }
    }
    //Détail d'un document
    /**
    * @OA\Get(
    *   path="/api/documents/{uid}",
    *   tags={"Documents"},
    *   operationId="showDocs",
    *   description="Détail d'un document",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Détail d'un document."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function show($uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        // Vérifier si l'ID est présent et valide
        $document = Document::select('id', 'code', $user->lg . ' as label', 'amount', 'deadline', 'description_' . $user->lg . ' as description', 'status')
        ->where('uid', $uid)
        ->first();
        if (!$document) {
            Log::warning("Document::show - Aucun document trouvé pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        try {
            // Charger les files avec eager loading et les transformer directement
            $files = $document->files
            ->join('requestdocs', 'requestdocs.id','=','files.requestdoc_id')
            ->sortBy(['files.uid', $user->lg . ' as label', 'required', 'files.status'])
            ->map(function ($file) {
                return [
                    'uid' => $file->uid,
                    'label' => $file->label,
                    'required' => $file->required ? 'Requis' : 'facultatif',
                    'status' => $file->status ? 'Activé' : 'Désactivé',
                ];
            })
            ->values()
            ->all();
            // Retourner les détails du document avec les files
            return $this->sendSuccess('Détails sur le document', [
                'code' => $document->code,
                'label' => $document->label,
                'amount' => $document->amount,
                'deadline' => $document->deadline,
                'description' => $document->description,
                'status' => $document->status ? 'Activé' : 'Désactivé',
                'files' => $files,
            ]);
        } catch(\Exception $e) {
            Log::warning("Document::show - Erreur d'affichage d'un document : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage d'un document");
        }
    }
    //Enregistrement
    /**
    * @OA\Post(
    *   path="/api/documents",
    *   tags={"Documents"},
    *   operationId="storeDocs",
    *   description="Enregistrement d'un document",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"code", "en", "fr", "description_en", "description_fr", "files"},
    *         @OA\Property(property="code", type="string"),
    *         @OA\Property(property="en", type="string"),
    *         @OA\Property(property="fr", type="string"),
    *         @OA\Property(property="amount", type="string"),
    *         @OA\Property(property="deadline", type="string"),
    *         @OA\Property(property="description_en", type="text"),
    *         @OA\Property(property="description_fr", type="text"),
    *         @OA\Property(property="files", type="array", @OA\Items(
    *               @OA\Property(property="requestdoc_id", type="integer"),
    *               @OA\Property(property="required", type="integer"),
    *               example="[1|1, 2|1, 3|0]"
    *           )
    *         ),
    *      )
    *   ),
    *   @OA\Response(response=200, description="Document enregisté avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function store(Request $request): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Document::store - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:5|unique:documents,code',
            'en' => 'required|string|max:255|unique:documents,en',
            'fr' => 'required|string|max:255|unique:documents,fr',
            'amount' => 'present',
            'deadline' => 'present',
            'description_en' => 'required',
            'description_fr' => 'required',
            'files' => 'required|array',
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Document::store - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Création de la reclamation
        $set = [
            'status' => 1,
            'en' => $request->en,
            'fr' => $request->fr,
            'code' => $request->code,
            'created_user' => $user->id,
            'amount' => $request->amount ?? '',
            'deadline' => $request->deadline ?? '',
            'description_en' => $request->description_en,
            'description_fr' => $request->description_fr,
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            $document = Document::create($set);
            // Valider la transaction
            DB::commit();
            // Si des fichiers sont fournies, les associer au profil
            if ($request->has('files') && is_array($request->files)) {
                foreach ($request->files as $files) {
                    $file = Str::of($files)->explode('|');
                    // Enregistrer le fichier
                    File::firstOrCreate([
                        'document_id' => $document->id,
                        'requestdoc_id' => $file[0],
                        'required' => $file[1],
                    ]);
                }
            }
            return $this->sendSuccess("Document enregistré avec succès.", [
                'code' => $request->code,
                'en' => $request->en,
                'fr' => $request->fr,
                'amount' => $request->amount,
                'deadline' => $request->deadline,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("Document::store : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de l'enregistrement du document.");
        }
    }
    // Modification
    /**
    * @OA\Put(
    *   path="/api/documents/{uid}",
    *   tags={"Documents"},
    *   operationId="editDocs",
    *   description="Modification d'un document",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"code", "en", "fr", "description_en", "description_fr", "files", "status"},
    *         @OA\Property(property="code", type="string"),
    *         @OA\Property(property="en", type="string"),
    *         @OA\Property(property="fr", type="string"),
    *         @OA\Property(property="amount", type="string"),
    *         @OA\Property(property="deadline", type="string"),
    *         @OA\Property(property="description_en", type="text"),
    *         @OA\Property(property="description_fr", type="text"),
    *         @OA\Property(property="status", type="integer"),
    *         @OA\Property(property="files", type="array", @OA\Items(
    *               @OA\Property(property="requestdoc_id", type="integer"),
    *               @OA\Property(property="required", type="integer"),
    *               example="[1|1, 2|1, 3|0]"
    *           )
    *         ),
    *      )
    *   ),
    *   @OA\Response(response=200, description="Document modifié avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function update(request $request, $uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Document::update - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:5|unique:documents,code,' . $uid,
            'en' => 'required|string|max:255|unique:documents,en,' . $uid,
            'fr' => 'required|string|max:255|unique:documents,fr,' . $uid,
            'amount' => 'present',
            'deadline' => 'present',
            'description_en' => 'required',
            'description_fr' => 'required',
            'status' => 'required|integer|in:0,1',
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Document::update - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Vérifier si l'ID est présent et valide
        $document = Document::where('uid', $uid)->first();
        if (!$document) {
            Log::warning("Document::update - Aucun document trouvé pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        // Création de la reclamation
        $set = [
            'en' => $request->en,
            'fr' => $request->fr,
            'code' => $request->code,
            'updated_user' => $user->id,
            'status' => $request->status,
            'amount' => $request->amount ?? '',
            'deadline' => $request->deadline ?? '',
            'description_en' => $request->description_en,
            'description_fr' => $request->description_fr,
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            $document->update($set);
            // Valider la transaction
            DB::commit();
            // Si des fichiers sont fournies, les associer au profil
            if ($request->has('files') && is_array($request->files)) {
                // Supprimer les fichiers existantes pour ce document
                File::where('document_id', $document->id)->delete();
                foreach ($request->files as $files) {
                    $file = Str::of($files)->explode('|');
                    // Enregistrer le fichier
                    File::firstOrCreate([
                        'document_id' => $document->id,
                        'requestdoc_id' => $file[0],
                        'required' => $file[1],
                    ]);
                }
            }
            return $this->sendSuccess("Document modifié avec succès.", [
                'code' => $request->code,
                'en' => $request->en,
                'fr' => $request->fr,
                'amount' => $request->amount,
                'deadline' => $request->deadline,
                'description_en' => $request->description_en,
                'description_fr' => $request->description_fr,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("Document::update : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de l'enregistrement du document.");
        }
	}
    // Suppression d'un document
    /**
    *   @OA\Delete(
    *   path="/api/documents/{uid}",
    *   tags={"Documents"},
    *   operationId="deleteDocs",
    *   description="Suppression d'un document",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Document supprimé avec succès."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function destroy($uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("Document::destroy - ID User : {$user->id} - Requête : " . $uid);
        try {
            // Vérification si le document est attribué à une demande
            $document = Document::select('documents.id', 'document_id')
            ->where('documents.uid', $uid)
            ->leftJoin('demands', 'demands.document_id','=','documents.id')
            ->first();
            if ($document->document_id != null) {
                Log::warning("Document::destroy - Tentative de suppression d'un document déjà attribué à une demande : " . $uid);
                return $this->sendError("Document est déjà attribué à une demande.", [], 403);
            }
            // Suppression
            $deleted = document::destroy($document->id);
            if (!$deleted) {
                Log::warning("Document::destroy - Tentative de suppression d'un document inexistante : " . $uid);
                return $this->sendError("Impossible de supprimer le document.", [], 403);
            }
            File::where('document_id', $document->id)->delete();
            return $this->sendSuccess("Document supprimé avec succès.");
        } catch(\Exception $e) {
            Log::warning("Document::destroy - Erreur lors de la suppression d'un document : " . $e->getMessage());
            return $this->sendError("Erreur lors de la suppression d'un document.");
        }
    }
}
