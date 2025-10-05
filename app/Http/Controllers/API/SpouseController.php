<?php
namespace App\Http\Controllers\API; 

use \Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{App, Auth, DB, Log, Validator};
use App\Models\{Cells, Country, District, MaritalStatus, Nationality, Permission, Profile, Province, Sector, Spouse, User};
use App\Http\Controllers\API\BaseController as BaseController;

class SpouseController extends BaseController
{
    // Liste des Conjoints
    /**
    * @OA\Get(
    *   path="/api/spouses",
    *   tags={"Spouses"},
    *   operationId="listSpouse",
    *   description="Liste des Conjoints",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Liste des Conjoints."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function index(): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        try {
            // Récupérer les données
            $query = User::select('users.uid', 'lastname', 'firstname', 'gender', 'number', 'email', 'rank', 'spouses.created_at')
            ->join('spouses', 'spouses.spouse_id','=','users.id')
            ->where('user_id', $user->id)
            ->get();
            // Vérifier si les données existent
            if ($query->isEmpty()) {
                Log::warning("User::index - Aucun conjoint trouvé");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            // Transformer les données
            $data = $query->map(fn($data) => [
                'uid' => $data->uid,
                'lastname' => $data->lastname,
                'firstname' => $data->firstname,
                'gender' => $data->gender,
                'number' => $data->number,
                'email' => $data->email,
                'rank' => $data->rank,
                'date' => Carbon::parse($data->created_at)->format('d/m/Y H:i'),
            ]);
            return $this->sendSuccess('Liste des Conjoints.', $data);
        } catch(\Exception $e) {
            Log::warning("User::index - Erreur d'affichage du conjoint : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage du conjoint");
        }
    }
    // Détail du conjoint
    /**
    * @OA\Get(
    *   path="/api/spouses/{uid}",
    *   tags={"Spouses"},
    *   operationId="showSpouse",
    *   description="Détail du conjoint",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Détail du conjoint."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function show($uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        try {
            // Récupérer les données
            $query = User::where('uid', $uid)->first();
            if (!$query) {
                Log::warning("User::show - Aucun conjoint trouvé pour l'ID : " . $uid);
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            // Conjoint
            $spouses = Spouse::where('uid', $uid)->first();
            // Cellules
            $cells = Cells::where('id', $query->cellule_id)->first();
            // Sectors
            $sector = Sector::where('id', $cells->sector_id)->first();
            // Districts
            $district = District::where('id', $sector->district_id)->first();
            // Provinces
            $province = Province::select('id', $user->lg . ' as label')
            ->where('id', $district->province_id)
            ->first();
            // Villes
            $towns = District::where('id', $query->town_id)->first();
            // Régions
            $region = Province::select('id', $user->lg . ' as label', 'country_id')
            ->where('id', $towns->province_id)
            ->first();
            // Pays
            $country = Country::select('id', $user->lg . ' as label', 'alpha')
            ->where('id', $region->country_id)
            ->first();
            // Situation matrimoniale
            $maritalstatus = MaritalStatus::select('id', $user->lg . ' as label')
            ->where('id', $query->maritalstatus_id)
            ->first();
            $data = [
                'lastname' => $query->lastname,
                'firstname' => $query->firstname,
                'gender' => $query->gender,
                'number' => $query->number,
                'email' => $query->email,
                'birthday_at' => Carbon::parse($query->birthday_at)->format('d/m/Y'),
                'birthplace' => $query->birthplace,
                'profession' => $query->profession,
                'village' => $query->village,
                'street_number' => $query->street_number,
                'house_number' => $query->house_number,
                'family_number' => $query->family_number,
                'register_number' => $query->register_number,
                'bp' => $query->bp,
                'diplome' => $query->diplome,
                'distinction' => $query->distinction,
                'fullname_father' => $query->fullname_father,
                'fullname_mother' => $query->fullname_mother,
                'fullname_person' => $query->fullname_person,
                'number_person' => $query->number_person,
                'residence_person' => $query->residence_person,
                'comment' => $query->comment,
                'created_at' => Carbon::parse($query->created_at)->format('d/m/Y H:i'),
                'photo' => env('APP_URL') . '/assets/photos/' . $query->photo,
                'cells' => [
                    'id' => $cells->id,
                    'label' => $cells->label,
                ],
                'sectors' => [
                    'id' => $sector->id,
                    'label' => $sector->label,
                ],
                'districts' => [
                    'id' => $district->id,
                    'label' => $district->label,
                ],
                'provinces' => [
                    'id' => $province->id,
                    'label' => $province->label,
                ],
                'towns' => [
                    'id' => $towns->id,
                    'label' => $towns->label,
                ],
                'regions' => [
                    'id' => $region->id,
                    'label' => $region->label,
                ],
                'countries' => [
                    'id' => $country->id,
                    'label' => $country->label,
                    'alpha' => $country->alpha,
                ],
                'maritalstatus' => [
                    'id' => $maritalstatus->id,
                    'label' => $maritalstatus->label,
                ],
            ];
            // Nationalité
            $data['nationality'] = '';
            if ($query->nationality_id != 0) {
                $nationality = Nationality::select('id', $user->lg . ' as label')
                ->where('id', $query->nationality_id)
                ->first();
                $data['nationality'] = [
                    'id' => $nationality->id,
                    'label' => $nationality->label,
                ];
            }
            // Profile
            $data['profile'] = '';
            if ($query->profile_id != 0) {
                $profile = Profile::select('id', $user->lg . ' as label')
                ->where('id', $query->profile_id)
                ->first();
                $data['profile'] = [
                    'id' => $profile->id,
                    'label' => $profile->label,
                ];
            }
            return $this->sendSuccess('Détail sur un conjoint.', $data);
        } catch(\Exception $e) {
            Log::warning("User::show - Erreur d'affichage du conjoint : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage du conjoint");
        }
    }
    //Modification
    /**
    * @OA\Put(
    *   path="/api/spouses/{uid}",
    *   tags={"Spouses"},
    *   operationId="editSpouse",
    *   description="Modification d'un conjoint",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *              required={"lastname", "firstname", "gender", "number", "email", "birthday", "birthplace", "profession", "village", "street_number", "house_number", "family_number", "fullname_person", "number_person", "residence_person", "cellule_id", "maritalstatus_id", "town_id", "fullname_father", "fullname_mother", "status", "profile_id"},
    *             @OA\Property(property="lastname", type="string"),
    *             @OA\Property(property="firstname", type="string"),
    *             @OA\Property(property="gender", type="string"),
    *             @OA\Property(property="number", type="string"),
    *             @OA\Property(property="email", type="string"),
    *             @OA\Property(property="birthday", type="date"),
    *             @OA\Property(property="birthplace", type="string"),
    *             @OA\Property(property="profession", type="string"),
    *             @OA\Property(property="village", type="string"),
    *             @OA\Property(property="street_number", type="string"),
    *             @OA\Property(property="house_number", type="string"),
    *             @OA\Property(property="family_number", type="integer"),
    *             @OA\Property(property="register_number", type="integer"),
    *             @OA\Property(property="fullname_person", type="string"),
    *             @OA\Property(property="number_person", type="string"),
    *             @OA\Property(property="residence_person", type="string"),
    *             @OA\Property(property="cellule_id", type="integer"),
    *             @OA\Property(property="town_id", type="integer"),
    *             @OA\Property(property="bp", type="string"),
    *             @OA\Property(property="diplome", type="string"),
    *             @OA\Property(property="distinction", type="string"),
    *             @OA\Property(property="maritalstatus_id", type="integer"),
    *             @OA\Property(property="nationality_id", type="integer"),
    *             @OA\Property(property="fullname_father", type="string"),
    *             @OA\Property(property="fullname_mother", type="string"),
    *             @OA\Property(property="comment", type="string"),
    *             @OA\Property(property="status", type="integer"),
    *             @OA\Property(property="profile_id", type="integer"),
    *      )
    *   ),
    *   @OA\Response(response=200, description="conjoint modifié avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function update(Request $request, $uid): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("User::update - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'lastname' => 'required',
            'firstname' => 'required',
            'gender' => 'required|in:M,F',
            'number' => 'required|unique:users,number,' . $uid . ',uid',
            'email' => 'required|unique:users,email,' . $uid . ',uid',
            'birthday' => 'required|date_format:Y-m-d',
            'birthplace' => 'required',
            'profession' => 'required',
            'village' => 'required',
            'street_number' => 'required',
            'house_number' => 'required',
            'family_number' => 'required',
            'fullname_person' => 'required',
            'number_person' => 'required',
            'fullname_father' => 'required',
            'fullname_mother' => 'required',
            'residence_person' => 'required',
            'maritalstatus_id' => 'required|min:1',
            'cellule_id' => 'required|min:1',
            'town_id' => 'required|min:1',
            'status' => 'required|in:1,2',
            'profile_id' => 'required|min:1',
        ]);
        //Error field
        if ($validator->fails()) {
            Log::warning("User::update - Validator : " . json_encode($request->all()));
            return $this->sendError('Champs invalides.', $validator->errors(), 422);
        }
        // Vérifier si l'ID est présent et valide
        $query = User::where('uid', $uid)->first();
        if (!$query) {
            Log::warning("User::update - Aucun conjoint trouvé pour l'ID : " . $uid);
            return $this->sendSuccess("Aucune donnée trouvée.");
        }
        // Formatage du nom et prénoms
        $email = Str::lower($request->email);
        $lastname = mb_strtoupper($request->lastname, 'UTF-8');
        $firstname = mb_convert_case(Str::lower($request->firstname), MB_CASE_TITLE, "UTF-8");
        // Génération de password
        $alfa = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
        $password = substr(str_shuffle($alfa), 0, 10);
        // Formatage des données
        $set = [
            'lastname' => $lastname,
            'firstname' => $firstname,
            'gender' => $request->gender,
            'number' => $request->number,
            'email' => $email,
            'birthday' => $request->birthday,
            'birthplace' => $request->birthplace,
            'profession' => $request->profession,
            'village' => $request->village,
            'street_number' => $request->street_number,
            'house_number' => $request->house_number,
            'family_number' => $request->family_number,
            'register_number' => $request->register_number,
            'fullname_person' => $request->fullname_person,
            'number_person' => $request->number_person,
            'fullname_father' => $request->fullname_father,
            'fullname_mother' => $request->fullname_mother,
            'residence_person' => $request->residence_person,
            'comment' => $request->comment,
            'maritalstatus_id' => $request->maritalstatus_id,
            'cellule_id' => $request->cellule_id,
            'town_id' => $request->town_id,
            'status' => $request->status,
            'profile_id' => $request->profile_id,
        ];
        // Test de modification de status
        $mail = 0;
        if ($query->status != $request->status) {
            switch ($request->status) {
                case 1:
                    $mail = 1;
                    $set['activated_at'] = now();
                    $set['activated_id'] = $user->id;
                    $set['password_at'] = now();
                    $set['password'] = Hash::make($password);
                    $status = __('message.activated');
                    break;
                case 2:
                    $mail = 2;
                    $set['blocked_at'] = now();
                    $set['blocked_id'] = $user->id;
                    $status = __('message.blocked');
                    break;
            }
        }
        DB::beginTransaction(); // Démarrer une transaction
        try {
            // Création du conjoint
            $query->update($set);
            DB::commit(); // Valider la transaction
            // Test send mail
            if ($mail != 0) {
                // Gender
                if ($request->gender == 'M')
                    $gender = __('message.mr');
                else
                    $gender = __('message.mrs');
                //subject
                $subject = __('message.actifaccount');
                $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
                . __('message.dear') . " " . $gender . " " . $request->lastname . ",<br><br>"
                . __('message.stataccount') . "<b>" . $status . "</b>" . "<br><br>";
                // Test send mail
                if ($mail == 1) {
                    $message .= __('message.paraconn') . " !<br>
                    <b>" . __('message.login') . " : </b>" . $email . "/" . $request->number . "<br>
                    <b>" . __('message.password') . " : </b>" . $password . "<br><br>";
                }
                $message .= __('message.bestregard') . " !<br>
                <hr style='color:#156082;'>
                </div>";
                // Envoi de l'email
                $this->sendMail($email, '', $subject, $message);
            }
            // Retourner les données du conjoint
            $data = [
                'lastname' => $lastname,
                'firstname' => $firstname,
                'gender' => $request->gender,
                'number' => $request->number,
                'email' => $email,
            ];
            return $this->sendSuccess('conjoint modifié avec succès.', $data, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::update - Erreur lors de la modification du conjoint : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de la modification du conjoint");
        }
	}
}