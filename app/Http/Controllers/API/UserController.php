<?php
namespace App\Http\Controllers\API; 

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use App\Models\{Parents, Permission, Profile, User};
use Illuminate\Support\Facades\{App, Auth, DB, Hash, Log, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

/**
 * @OA\Info(title="API RWANDA", version="1.0")
 */
class UserController extends BaseController
{
//Account creation
/**
* @OA\Post(
*   path="/api/users/register",
*   tags={"Users"},
*   operationId="register",
*   description="Account creation",
 *   @OA\RequestBody(
 *      required=true,
 *      @OA\MediaType(
 *          mediaType="multipart/form-data",
 *          @OA\Schema(
 *          required={"lg", "lastname", "firstname", "gender", "number", "email", "birthday", "birthplace", "profession", "village", "street_number", "hourse_number", "family_number", "fullname_peson", "number_person", "residence_person", "cellule_id", "maritalstatus_id", "district_id", "fullname_father", "fullname_mother", "photo"},
    *         @OA\Property(property="lg", type="string"),
    *         @OA\Property(property="lastname", type="string"),
    *         @OA\Property(property="firstname", type="string"),
    *         @OA\Property(property="gender", type="string"),
    *         @OA\Property(property="number", type="string"),
    *         @OA\Property(property="email", type="string"),
    *         @OA\Property(property="birthday", type="date"),
    *         @OA\Property(property="birthplace", type="string"),
    *         @OA\Property(property="profession", type="string"),
    *         @OA\Property(property="village", type="string"),
    *         @OA\Property(property="street_number", type="string"),
    *         @OA\Property(property="hourse_number", type="string"),
    *         @OA\Property(property="family_number", type="integer"),
    *         @OA\Property(property="fullname_peson", type="string"),
    *         @OA\Property(property="number_person", type="string"),
    *         @OA\Property(property="residence_person", type="string"),
    *         @OA\Property(property="cellule_id", type="integer"),
    *         @OA\Property(property="district_id", type="integer"),
    *         @OA\Property(property="bp", type="string"),
    *         @OA\Property(property="diplome", type="string"),
    *         @OA\Property(property="distinction", type="string"),
    *         @OA\Property(property="maritalstatus_id", type="integer"),
    *         @OA\Property(property="nationality_id", type="integer"),
    *         @OA\Property(property="fullname_father", type="string"),
    *         @OA\Property(property="fullname_mother", type="string"),
    *         @OA\Property(property="photo", type="string", format="binary"),
    *          )
    *      )
    *   ),
    *   @OA\Response(response=200, description="Création de compte éffectuée avec succès."),
    *   @OA\Response(response=401, description="Echec de Création de compte."),
    *   @OA\Response(response=404, description="Page introuvable."),
 * )
 */
    public function store(Request $request): JsonResponse
    {
        Log::notice("User::store : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'lg' => 'required',
            'lastname' => 'required',
            'firstname' => 'required',
            'gender' => 'required|in:M,F',
            'number' => 'required|unique:users,number',
            'email' => 'required|email|unique:users,email',
            'birthday' => 'required|date_format:Y-m-d',
            'birthplace' => 'required',
            'profession' => 'required',
            'village' => 'required',
            'street_number' => 'required',
            'hourse_number' => 'required',
            'family_number' => 'required',
            'fullname_peson' => 'required',
            'number_person' => 'required',
            'fullname_father' => 'required',
            'fullname_mother' => 'required',
            'residence_person' => 'required',
            'maritalstatus_id' => 'required',
            'cellule_id' => 'required',
            'district_id' => 'required',
			'photo' => 'required|image|mimes:png,jpeg,jpg|max:2048',
        ]);
        //Error field
        if ($validator->fails()) {
            Log::warning("User::store - Validator : " . json_encode($request->all()));
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        // Upload photo
        $dir = 'assets/photos';
        $image = $request->file('photo');
        $ext = $image->getClientOriginalExtension();
        $photo = User::filenameUnique($ext);
        if(!($image->move($dir, $photo))){
            Log::warning("User::store - Erreur de téléchargement de la photo : " . $e->getMessage() . " " . json_encode($request->all()));
            return $this->sendError("Erreur de téléchargement de la photo.");
        }
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
            'photo' => $photo,
            'lg' => $request->lg,
            'cellule_id' => $request->cellule_id,
            'district_id' => $request->district_id,
            'nationality_id' => $request->nationality_id,
            'maritalstatus_id' => $request->maritalstatus_id,
        ];
        // return $this->sendError("Data insert", $set);
        DB::beginTransaction(); // Démarrer une transaction
        try {
            // Création de l'utilisateur
            $user = User::create($set);
            DB::commit(); // Valider la transaction
            // Retourner les données de l'utilisateur
            // Father
            Parents::create([
                'type_id' => 1,
                'user_id' => $user->id,
                'fullname' => $request->fullname_father,
            ]);
            // Mother
            Parents::create([
                'type_id' => 2,
                'user_id' => $user->id,
                'fullname' => $request->fullname_mother,
            ]);
            $data = [
                'lastname' => $lastname,
                'firstname' => $firstname,
                'gender' => $request->gender,
                'number' => $request->number,
                'email' => $email,
            ];
            /*
            // Gender
            if ($request->gender == 'M')
                $gender = __('message.mr');
            else
                $gender = __('message.mrs');
            //subject
            $subject = __('message.creataccount');
            $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
            . __('message.dear') . " " . $gender ." ".$request->lastname.",<br><br>"
            . __('message.otp') . " : <b>" . __('message.txtaccount') . "</b><br><br>"
            . __('message.bestregard') . " !<br>
            <hr style='color:#156082;'>
            </div>";
            // Envoi de l'email
            $this->sendMail($request->email, '', $subject, $message);
            
            // Gender
            if ($request->gender == 'M')
                $gender = __('message.mr');
            else
                $gender = __('message.mrs');
            //subject
            $subject = __('message.creataccount');
            $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
            . __('message.dear') . " " . $gender ." ".$request->lastname.",<br><br>"
            . __('message.otp') . " : <b>" . __('message.txtaccount') . "</b><br><br>"
            . __('message.bestregard') . " !<br>
            <hr style='color:#156082;'>
            </div>";
            // Envoi de l'email
            $this->sendMail($request->email, '', $subject, $message);
            */
            return $this->sendSuccess('Utilisateur enregistré avec succès.', $data, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::store - Erreur enregistrement de l'utilisateur : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur enregistrement de l'utilisateur");
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
                $data['auth_token'] =  $user->createToken('MyApp')->accessToken;
                $data['infos'] = [
                    'lastname' => $user->lastname,
                    'firstname' => $user->firstname,
                    'number' => $user->number,
                    'email' => $user->email,
                    'profile' => $request->lg == 'en' ? $profil->label_en : $profil->label_fr,
                    'photo' => env('APP_URL') . '/assets/photos/' . $user->photo,
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