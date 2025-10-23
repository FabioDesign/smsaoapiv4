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
    //Account creation
    /**
    * @OA\Post(
    *   path="/api/users/register",
    *   tags={"Users"},
    *   operationId="registerUser",
    *   description="Account creation",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *             required={"lg", "lastname", "firstname", "number", "email", "town_id", "accountyp_id"},
    *             @OA\Property(property="lg", type="string"),
    *             @OA\Property(property="lastname", type="string"),
    *             @OA\Property(property="firstname", type="string"),
    *             @OA\Property(property="number", type="string"),
    *             @OA\Property(property="email", type="string"),
    *             @OA\Property(property="town_id", type="integer"),
    *             @OA\Property(property="accountyp_id", type="integer"),
    *             @OA\Property(property="company", type="string"),
    *             @OA\Property(property="nif", type="string"),
    *             @OA\Property(property="address", type="string"),
    *             @OA\Property(property="website", type="string"),
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
            'lg' => 'required|in:pt,en',
            'lastname' => 'required',
            'firstname' => 'required',
            'number' => 'required|unique:users,number',
            'email' => 'required|email|unique:users,email',
            'town_id' => 'required|integer|min:1',
            'accountyp_id' => 'required|integer|min:1',
            // 'g_recaptcha_response' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if ($validator->fails()) {
            Log::warning("User::store - Validator : " . $validator->errors()->first() . " - ".json_encode($request->all()));
            return $this->sendSuccess(__('message.fielderr'), $validator->errors(), 422);
        }
        // Test sur DID
        if ($request->accountyp_id != 1) {
            // Validator
            $validator = Validator::make($request->all(), [
                'company' => 'required',
                'nif' => 'required',
                'address' => 'required',
                'website' => 'required',
            ]);
            // Error field
            if ($validator->fails()) {
                Log::warning("User::store - Validator : " . $validator->errors()->first() . " - ".json_encode($request->all()));
                return $this->sendError(__('message.fielderr'), $validator->errors()->first(), 422);
            }
        }
        /*
        // Paramètre de Recapcha
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'remoteip' => $request->ip(),
            'secret' => env('RECAPTCHAV3_SECRET'),
            'response' => $_POST['g_recaptcha_response'],
        ];
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $resultJson = json_decode($result);
        if ($resultJson->success == true) {
        */
            // Formatage du nom et prénoms
            $email = Str::lower($request->email);
            $set = [
                'lg' => $request->lg,
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'number' => $request->number,
                'email' => $email,
                'town_id' => $request->town_id,
                'accountyp_id' => $request->accountyp_id,
                'company' => $request->company ?? '',
                'nif' => $request->nif ?? '',
                'address' => $request->address ?? '',
                'website' => $request->website ?? '',
                'password_at' => now(),
                'password' => Hash::make($request->password),
            ];
            DB::beginTransaction(); // Démarrer une transaction
            try {
                // Création de l'utilisateur
                $user = User::create($set);
                DB::commit(); // Valider la transaction
                // Username
                $username = $request->firstname . " " . $request->lastname;
                // Subject
                $subject = __('message.creataccount');
                // Send SMS to LogicMind
                $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>
                Dear Mr.,<br /><br />
                Confirmation mail of registration of <b>" . $username . "</b><br />
                Contact : <b>" . $request->number . "</b><br />
                Email : <b>" . $email . "</b><br />";
                if ($request->accountyp_id != 1) $message .= "Business Name : <b>" . $request->company . "</b><br />";
                $message .= env('MAIL_SIGNATURE') . "</div>";
                // Envoi de l'email
                $this->sendMail(env('MAIL_FROM_ADDRESS'), $email, $username, env('MAIL_CC'), $subject, $message);

                //send SMS to Customer
                if ($request->lg == 'en') {
                    $content = "Dear M./Mrs. " . $username . "<br /><br />
                    Thank you for your registration on SMS illico, our platform of sending SMS through the web.<br />
                    Your registration has been taken into account and will be validated within 48 hours maximum after verification of provided information.<br />
                    You will receive an SMS and a mail after the activation of your account.<br /><br />Best Regards.<br />LOGICMIND, LDA";
                } else {
                    $content = "Prezado(a) Sr.(a) " . $username . "<br /><br />
                    Obrigado pelo seu registo no SMS illico, a nossa plataforma de envio de SMS através da web.<br />
                    O seu registo foi registado e será validado no prazo máximo de 48 horas após a verificação das informações fornecidas.<br />
                    Receberá um SMS e um e-mail após a ativação da sua conta.<br /><br />Cumprimentos.<br />LOGICMIND, LDA";
                }
                $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>
                " . $content . env('MAIL_SIGNATURE') . "</div>";
                // Envoi de l'email
                $this->sendMail($email, env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'), env('MAIL_CC'), $subject, $message);
                // Retourner les données de l'utilisateur
                $data = [
                    'lastname' => $request->lastname,
                    'firstname' => $request->firstname,
                    'number' => $request->number,
                    'email' => $email,
                ];
                return $this->sendSuccess(__('message.saveusrsucc'), $data, 201);
            } catch (\Exception $e) {
                DB::rollBack(); // Annuler la transaction en cas d'erreur
                Log::warning("User::store - Erreur enregistrement de l'utilisateur : " . $e->getMessage() . " " . json_encode($set));
                return $this->sendError(__('message.saveusrerr'));
            }
        /*
        } else {
            Log::warning("User::store - Recaptcha : " . json_encode($resultJson));
            return $this->sendError("Recaptcha erroné, veuillez réessayer svp.");
        }
        */
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
    *             required={"lastname", "firstname", "number", "email", "town_id", "accountyp_id"},
    *             @OA\Property(property="lastname", type="string"),
    *             @OA\Property(property="firstname", type="string"),
    *             @OA\Property(property="number", type="string"),
    *             @OA\Property(property="email", type="string"),
    *             @OA\Property(property="town_id", type="integer"),
    *             @OA\Property(property="accountyp_id", type="integer"),
    *             @OA\Property(property="company", type="string"),
    *             @OA\Property(property="nif", type="string"),
    *             @OA\Property(property="address", type="string"),
    *             @OA\Property(property="website", type="string"),
    *      )
    *   ),
    *   @OA\Response(response=200, description="Profil utilisateur modifié avec succès."),
    *   @OA\Response(response=400, description="Bad Request."),
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
            'number' => 'required|unique:users,number,'.$user->id,
            'email' => 'required|unique:users,email,'.$user->id,
            'town_id' => 'required|integer|min:1',
            'accountyp_id' => 'required|integer|min:1',
        ]);
		App::setLocale($user->lg);
        //Error field
        if ($validator->fails()) {
            Log::warning("User::profil - Validator : " . $validator->errors()->first() . " - ".json_encode($request->all()));
            return $this->sendSuccess(__('message.fielderr'), $validator->errors(), 422);
        }
        // Test sur DID
        if ($request->accountyp_id != 1) {
            // Validator
            $validator = Validator::make($request->all(), [
                'company' => 'required',
                'nif' => 'required',
                'address' => 'required',
                'website' => 'required',
            ]);
            // Error field
            if ($validator->fails()) {
                Log::warning("User::profil - Validator : " . $validator->errors()->first() . " - ".json_encode($request->all()));
                return $this->sendError(__('message.fielderr'), $validator->errors()->first(), 422);
            }
        }
        // Formatage du nom et prénoms
        $email = Str::lower($request->email);
        // Formatage des données
        $set = [
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'number' => $request->number,
            'email' => $email,
            'town_id' => $request->town_id,
            'accountyp_id' => $request->accountyp_id,
            'company' => $request->company ?? '',
            'nif' => $request->nif ?? '',
            'address' => $request->address ?? '',
            'website' => $request->website ?? '',
        ];
        DB::beginTransaction(); // Démarrer une transaction
        try {
            // Création de l'utilisateur
            User::findOrFail($user->id)->update($set);
            DB::commit(); // Valider la transaction
            return $this->sendSuccess(__('message.profilsucc'), $set, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::profil - Erreur lors de la modification de Profil utilisateur : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError(__('message.profilerr'));
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
            Log::warning("User::login - Validator : " . $validator->errors()->first() . " - ".json_encode($request->all()));
          return $this->sendSuccess(__('message.fielderr'), $validator->errors(), 422);
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
                // Ajouter les informations de l'utilisateur et du profil dans la réponse
                $data = [
                    'access_token' =>  $user->createToken('MyApp')->accessToken,
                    'infos' => [
                        'lastname' => $user->lastname,
                        'firstname' => $user->firstname,
                        'number' => $user->number,
                        'email' => $user->email,
                        'photo' => env('APP_URL') . '/assets/photos/' . $user->photo,
                    ]
                ];
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