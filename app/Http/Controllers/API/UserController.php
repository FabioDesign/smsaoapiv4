<?php
namespace App\Http\Controllers\API; 

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use App\Models\{Permission, Profile, User};
use Illuminate\Support\Facades\{App, Auth, DB, Hash, Log, Session, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

/**
 * @OA\Info(title="API RWANDA", version="1.0")
 */
class UserController extends BaseController
{
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
    *         required={"login", "password"},
    *         @OA\Property(property="login", type="string"),
    *         @OA\Property(property="password", type="string")
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
          return $this->sendError('Champs invalides.', $validator->errors());
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
                $profil = Profile::find($user->profile_id);
                // Vérifier si le profil existe
                if (!$profil) {
                    Log::warning("Aucun profil trouvé pour l'utilisateur : " . $user->id);
                    return $this->sendError(__('message.noprofil'), [], 404);
                }
                // Ajouter les informations de l'utilisateur et du profil dans la réponse
                $data['auth_token'] =  $user->createToken('MyApp')->accessToken;
                $data['infos'] = [
                    'lastname' => $user->lastname,
                    'firstname' => $user->firstname,
                    'number' => $user->number,
                    'email' => $user->email,
                    'profile' => $request->lg == 'en' ? $profil->label_en : $profil->label_fr,
                ];
                // Code to list permissions
                $permissions = Permission::select('menus.id', 'label_en', 'label_fr', 'target', 'icone')
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
                    'menu' => $request->lg == 'en' ? $permission->label_en : $permission->label_fr,
                    'target' => $permission->target,
                    'icone' => $permission->icone,
                ]);
                $data['permissions'] = $query;
                // Logs::createLog('Connexion', $user->id, 1);
                return $this->sendSuccess(__('message.authsucc'), $data);
            } catch (\Exception $e) {
                Log::warning("Echec de connexion à la base de données : " . $e->getMessage());
                return $this->sendError(__('message.error'));
            }
        } else {
            Log::warning("Authentication : " . json_encode($request->all()));
            return $this->sendError(__('message.authfail'), [], 401);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        Log::notice("User::store : " . json_encode($request->all()));
        // return $this->sendError("Data", $request->all());
        // $validator = Validator::make($request->all(), [
        //     'lastname' => 'required',
        //     'firstname' => 'required',
        //     'email' => 'required|email|unique:users,email',
        //     'password' => [
        //         'required', 'confirmed',
        //         Password::min(8)
        //             ->mixedCase() // Majuscules + minuscules
        //             ->letters()   // Doit contenir des lettres
        //             ->numbers()   // Doit contenir des chiffres
        //             ->symbols()   // Doit contenir des caractères spéciaux
        //     ],
        // ]);
        // //Error field
        // if ($validator->fails()) {
        //     Log::warning("User::store - Validator : " . json_encode($request->all()));
        //     return $this->sendError($validator->errors()->first());
        // }
        // Formatage du nom et prénoms
        $email = Str::lower($request->email);
        $lastname = mb_strtoupper($request->lastname, 'UTF-8');
        $firstname = mb_convert_case(Str::lower($request->firstname), MB_CASE_TITLE, "UTF-8");
        $set = [
            'lastname' => $lastname,
            'firstname' => $firstname,
            'gender' => $request->gender,
            'number' => $request->number,
            'email' => $email,
            'password' => Hash::make($request->password), // Hash du mot de passe
            'password_at' => now(),
            'birthday_at' => $request->birthday,
            'birthplace' => $request->birthplace,
            'profession' => $request->profession,
            'village' => $request->village,
            'street_number' => $request->street_number,
            'hourse_number' => $request->hourse_number,
            'family_number' => $request->family_number,
            'register_number' => $request->register_number,
            'bp' => $request->bp,
            'diplome' => $request->diplome,
            'distinction' => $request->distinction,
            'fullname_peson' => $request->fullname_peson,
            'number_person' => $request->number_person,
            'residence_person' => $request->residence_person,
            'photo' => $request->photo,
            'login_at' => now(),
            'comment' => $request->comment,
            'profile_id' => $request->profile_id,
            'cellule_id' => $request->cellule_id,
            'district_id' => $request->district_id,
            'nationality_id' => $request->nationality_id,
        ];
        // return $this->sendError("Data insert", $set);
        DB::beginTransaction(); // Démarrer une transaction
        try {
            // Création de l'utilisateur
            $user = User::create($set);
            DB::commit(); // Valider la transaction
            // Retourner les données de l'utilisateur
            $data = [
                'lastname' => $lastname,
                'firstname' => $firstname,
                'gender' => $request->gender,
                'number' => $request->number,
                'email' => $email,
            ];
            return $this->sendSuccess('Utilisateur enregistré avec succès.', $data, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::store - Erreur enregistrement de l'utilisateur : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur enregistrement de l'utilisateur");
        }
    }
}