<?php
namespace App\Http\Controllers\API; 

use \Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\{App, Auth, DB, Hash, Log, Validator};
use App\Models\{Cells, Country, District, MaritalStatus, Nationality, Permission, Profile, Province, Sector, User};
use App\Http\Controllers\API\BaseController as BaseController;

class UserController extends BaseController
{
    // Liste des Utilisateurs
    /**
    * @OA\Get(
    *   path="/api/users?num=1&limit=10&status=0",
    *   tags={"Users"},
    *   operationId="listUser",
    *   description="Liste des Utilisateurs",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Liste des Utilisateurs."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function index(Request $request): JsonResponse {
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        try {
            $num = isset($request->num) ? (int) $request->num:1;
            $limit = isset($request->limit) ? (int) $request->limit:10;
            $status = isset($request->status) ? (int) $request->status:'';
            // Récupérer les données
            $query = User::select('users.uid', 'lastname', 'firstname', 'number', 'email', $user->lg . ' as label', 'users.status', 'users.created_at')
            ->leftJoin('profiles', 'profiles.id','=','users.profile_id')
            ->where('profile_id', '!=', 1)
            ->where('users.id', '!=', $user->id)
            ->when(($status != ''), fn($q) => $q->where('users.status', $status))
            ->orderByDesc('users.created_at')
            ->paginate($limit, ['*'], 'page', $num);
            // Vérifier si les données existent
            if ($query->isEmpty()) {
                Log::warning("User::index - Aucun utilisateur trouvé");
                return $this->sendError("Aucune donnée trouvée.", [], 404);
            }
            // Transformer les données
            $data = $query->map(fn($data) => [
                'uid' => $data->uid,
                'lastname' => $data->lastname,
                'firstname' => $data->firstname,
                'number' => $data->number,
                'email' => $data->email,
                'profile' => $data->label,
                'status' => match((int)$data->status) {
                    0 => 'Inactif',
                    1 => 'Actif',
                    2 => 'Bloqué'
                },
                'date' => Carbon::parse($data->created_at)->format('d/m/Y H:i'),
            ]);
            return $this->sendSuccess('Liste des utilisateurs.', [
                'lists' => $data,
                'total'  => $query->total(),
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
            ]);
        } catch(\Exception $e) {
            Log::warning("User::index - Erreur d'affichage de l'utilisateur : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage de l'utilisateur");
        }
    }
    // Détail d'Utilisateur
    /**
    * @OA\Get(
    *   path="/api/users/{uid}",
    *   tags={"Users"},
    *   operationId="showUser",
    *   description="Détail d'Utilisateur",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Détail d'Utilisateur."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
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
                Log::warning("User::show - Aucun utilisateur trouvé pour l'ID : " . $uid);
                return $this->sendError("Aucune donnée trouvée.", [], 404);
            }
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
                'status' => match((int)$query->status) {
                    0 => 'Inactif',
                    1 => 'Actif',
                    2 => 'Bloqué'
                },
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
            return $this->sendSuccess('Détail sur un Utilisateur.', $data);
        } catch(\Exception $e) {
            Log::warning("User::show - Erreur d'affichage de l'utilisateur : ".$e->getMessage());
            return $this->sendError("Erreur d'affichage de l'utilisateur");
        }
    }
    //Modification
    /**
    * @OA\Put(
    *   path="/api/users/{uid}",
    *   tags={"Users"},
    *   operationId="editUser",
    *   description="Modification d'un Utilisateur",
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
    *   @OA\Response(response=200, description="Utilisateur modifié avec succès."),
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
            'number' => 'required|unique:users,number,'.$uid.',uid',
            'email' => 'required|unique:users,email,'.$uid.',uid',
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
            Log::warning("User::update - Aucun utilisateur trouvé pour l'ID : " . $uid);
            return $this->sendError("Aucune donnée trouvée.", [], 404);
        }
        // Formatage du nom et prénoms
        $email = Str::lower($request->email);
        $lastname = mb_strtoupper($request->lastname, 'UTF-8');
        $firstname = mb_convert_case(Str::lower($request->firstname), MB_CASE_TITLE, "UTF-8");
        // Génération de password
        $alfa = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789#@()_-{}*+';
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
            'password' => Hash::make($password),
        ];
        // Test de modification de status
        $mail = 0;
        if ($query->status != $request->status) {
            switch ($request->status) {
                case 1:
                    $mail = 1;
                    $set['active_at'] = now();
                    $set['active_id'] = $user->id;
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
            // Création de l'utilisateur
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
            return $this->sendSuccess('Utilisateur modifié avec succès.', $set, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::update - Erreur lors de la modification de l'utilisateur : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de la modification de l'utilisateur");
        }
	}
    //Modification
    /**
    * @OA\Post(
    *   path="/api/users/profil",
    *   tags={"Users"},
    *   operationId="profilUser",
    *   description="Modification du profil utilisateur",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *              required={"lastname", "firstname", "gender", "number", "email", "birthday", "birthplace", "profession", "village", "street_number", "house_number", "family_number", "fullname_person", "number_person", "residence_person", "cellule_id", "maritalstatus_id", "town_id", "fullname_father", "fullname_mother"},
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
    *      )
    *   ),
    *   @OA\Response(response=200, description="Profil utilisateur modifié avec succès."),
    *   @OA\Response(response=400, description="Erreur de validation."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function profil(Request $request): JsonResponse {
        //User
        $user = Auth::user();
        //Data
        Log::notice("User::profil - ID User : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'lastname' => 'required',
            'firstname' => 'required',
            'gender' => 'required|in:M,F',
            'number' => 'required|unique:users,number,'.$user->id,
            'email' => 'required|unique:users,email,'.$user->id,
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
        ]);
        //Error field
        if ($validator->fails()) {
            Log::warning("User::profil - Validator : " . json_encode($request->all()));
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        // Formatage du nom et prénoms
        $email = Str::lower($request->email);
        $lastname = mb_strtoupper($request->lastname, 'UTF-8');
        $firstname = mb_convert_case(Str::lower($request->firstname), MB_CASE_TITLE, "UTF-8");
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
            'fullname_person' => $request->fullname_person,
            'number_person' => $request->number_person,
            'fullname_father' => $request->fullname_father,
            'fullname_mother' => $request->fullname_mother,
            'residence_person' => $request->residence_person,
            'maritalstatus_id' => $request->maritalstatus_id,
            'cellule_id' => $request->cellule_id,
            'town_id' => $request->town_id,
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            // Création de l'utilisateur
            User::findOrFail($user->id)->update($set);
            DB::commit(); // Valider la transaction
            return $this->sendSuccess('Profil utilisateur modifié avec succès.', $set, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::profil - Erreur lors de la modification de Profil utilisateur : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur lors de la modification de Profil utilisateur");
        }
	}
    //Photo de profil
    /**
     * @OA\Post(
     *   path="/api/users/photo",
     *   tags={"Users"},
     *   operationId="photo",
     *   description="Modification de la photo de profil",
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *             required={"photo"},
     *             @OA\Property(property="photo", type="string", format="binary"),
     *          )
     *      )
     *   ),
     *   @OA\Response(response=200, description="Photo de profil modifiée avec succès."),
     *   @OA\Response(response=401, description="Non autorisé."),
     *   @OA\Response(response=404, description="Page introuvable."),
     * )
     */
    public function photo(Request $request)
    {
        $user = Auth::user();
        //Validator
        $validator = Validator::make($request->all(), [
			'photo' => 'required|file|mimes:png,jpeg,jpg|max:2048',
        ]);
		App::setLocale($user->lg);
        //Error field
        if ($validator->fails()) {
            Log::warning("User::photo - Validator : " . json_encode($request->all()));
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        // Upload photo
        $dir = 'assets/photos';
        $image = $request->file('photo');
        $ext = $image->getClientOriginalExtension();
        $photo = User::filenameUnique($ext);
        if (!($image->move($dir, $photo))) {
            Log::warning("User::photo - Erreur de téléchargement de la photo : " . $e->getMessage());
            return $this->sendError("Erreur de téléchargement de la photo.");
        }
        try {
            $set = [
                'photo' => $photo,
            ];
            User::findOrFail($user->id)->update($set);
            return $this->sendSuccess('Photo de profil modifiée avec succès.', [], 201);
        } catch(\Exception $e) {
            Log::warning("Photo::store - Erreur de modification de la photo de profil : " . $e->getMessage());
            return $this->sendError("Erreur de modification de la photo de profil");
        }
    }
    //Authentification
    /**
    * @OA\Post(
    *   path="/api/users/auth",
    *   tags={"Users"},
    *   operationId="login",
    *   description="Authenticate Platform and Generate JWT",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"login", "password", "lg"},
    *         @OA\Property(property="login", type="string"),
    *         @OA\Property(property="password", type="string"),
    *         @OA\Property(property="lg", type="string")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Authentification éffectuée avec succès."),
    *   @OA\Response(response=401, description="Echec d'authentification."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function login(Request $request): JsonResponse
    {
        //Validator
        $validator = Validator::make($request->all(), [
          'login' => 'required',
          'password' => 'required',
          'lg' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if ($validator->fails()) {
          Log::warning("User::login - Validator : ".$validator->errors());
          return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        $credentialNum = [
            'number' => $request->login,
            'password' => $request->password,
            'status' => 1,
        ];
        $credentialEml = [
            'email' => $request->login,
            'password' => $request->password,
            'status' => 1,
        ];
        if ((Auth::attempt($credentialNum))||(Auth::attempt($credentialEml))) {
            try {
                $user = Auth::user();
                // Vérifier si le profil existe
                $profil = Profile::find($user->profile_id);
                if (!$profil) {
                    Log::warning("Aucun profil trouvé pour l'utilisateur : " . $user->id);
                    return $this->sendError(__('message.noprofil'), [], 404);
                }
                // Ajouter les informations de l'utilisateur et du profil dans la réponse
                $data = [];
                $data['access_token'] =  $user->createToken('MyApp')->accessToken;
                $data['infos'] = [
                    'lastname' => $user->lastname,
                    'firstname' => $user->firstname,
                    'number' => $user->number,
                    'email' => $user->email,
                    'birthday_at' => Carbon::parse($user->birthday_at)->format('d/m/Y'),
                    'birthplace' => $user->birthplace,
                    'profile' => $request->lg == 'en' ? $profil->en : $profil->fr,
                    'photo' => env('APP_URL') . '/assets/photos/' . $user->photo,
                ];
                // Code to list permissions
                $permissions = Permission::select('menus.id', $request->lg . ' as label', 'target', 'icone')
                ->join('menus', 'menus.id', '=', 'permissions.menu_id')
                ->where('profile_id', $user->profile_id) // Seulement les menus du profil de l'utilisateur
                ->where('status', 1) // Seulement les menus activés
                ->where('action_id', 1) // Seulement les actions de voir
                ->orderBy('position')
                ->get();
                // Vérifier si les données existent
                if ($permissions->isEmpty()) {
                    Log::warning("Aucun menu trouvé pour ce profil : " . $user->profile_id);
                    return $this->sendError(__('message.nomenu'), [], 404);
                }
                // Transformer les données
                $query = $permissions->map(fn($permission) => [
                    'id' => $permission->id,
                    'menu' => $permission->label,
                    'target' => $permission->target,
                    'icone' => $permission->icone,
                ]);
                $data['permissions'] = $query;
                User::findOrFail($user->id)->update([
                    'login_at' => now(),
                    'lg' => $request->lg,
                ]);
                // Logs::createLog('Connexion', $user->id, 1);
                return $this->sendSuccess(__('message.authsucc'), $data);
            } catch (\Exception $e) {
                Log::warning("Echec de connexion à la base de données : " . $e->getMessage());
                return $this->sendError(__('message.error'));
            }
        } else {
            Log::warning("Authentication : " . json_encode($request->all()));
            return $this->sendError(__('message.autherr'), [], 401);
        }
    }
    //Déconnexion
    /**
    * @OA\Post(
    *   path="/api/users/logout",
    *   tags={"Users"},
    *   operationId="logout",
    *   description="Deconnecte l'utilisateur en supprimant son token d'accès",
    *   security={{"bearer":{}}},
    *   @OA\Response(response=200, description="Déconnexion éffectuée avec succès."),
    *   @OA\Response(response=401, description="Echec d'authentification."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();
            return $this->sendSuccess(__('message.logoutsucc'));
        } catch (\Exception $e) {
            Log::error("Logout error: " . $e->getMessage());
            return $this->sendError(__('message.logouterr'));
        }
    }
}