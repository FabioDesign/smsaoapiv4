<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Models\{Checkotp, Parents, User};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{App, DB, Hash, Log, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

class RegisterController extends BaseController
{
    //Renvoyer OTP",
    /**
    * @OA\Post(
    *   path="/api/register/sendotp",
    *   tags={"Register"},
    *   operationId="sendotp",
    *   description="Renvoyer OTP",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"email", "lg"},
    *         @OA\Property(property="email", type="string", example="fabio@yopmail.com"),
    *         @OA\Property(property="lg", type="string")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Renvoyer OTP."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function sendotp(Request $request): JsonResponse {
        //Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'lg' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if($validator->fails()){
            Log::warning("Send OTP - Validator email : ".$request->email);
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        // Générer l'OTP sécurisé
        $otp = random_int(100, 999) . ' ' . random_int(100, 999);
        // Formatage du nom et prénoms
        $email = Str::lower($request->email);
        if (Checkotp::where('email', $email)->exists()) {
            // Récupérer les données
            $user = Checkotp::where([
                ['email', $email],
                ['otp', $request->otp],
            ])
            ->first();
            // Vérifier si les données existent
            if (!$user) {
                Log::warning("Code OTP erroné : " . $email);
                return $this->sendError("Code OTP erroné.", [], 404);
            }
            // Vérifier si l'OTP a expiré
            if (!($user->otp_at >= now()->subMinutes(10))) {
                Log::warning("Code OTP a expiré : " . $email);
                return $this->sendError("Code OTP a expiré.", [], 404);
            }
        } else {
            $set = [
                'email' => $email,
                'otp' => str_replace(' ', '', $otp),
            ];
            DB::beginTransaction(); // Démarrer une transaction
            try {
                // Création de l'utilisateur
                $user = Checkotp::create($set);
                DB::commit(); // Valider la transaction
            } catch(\Exception $e) {
                Log::warning("Erreur de récupération de l'utilisateur : " . $e->getMessage());
                return $this->sendError(__('message.error'));
            }
        }
        //subject
        $subject = __('message.verifeml');
        $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
        . __('message.dear') . " " . __('message.mr_mrs') . ",<br><br>"
        . __('message.otp') . " : <b>" . $otp . "</b><br><br>"
        . __('message.bestregard') . " !<br>
        <hr style='color:#156082;'>
        </div>";
        try {
            // Envoi de l'email
            $this->sendMail($email, '', $subject, $message);
            return $this->sendSuccess(__('message.sendmailsucc'), [], 201);
        } catch(\Exception $e) {
            Log::warning("Erreur d'envoi de mail : " . $e->getMessage());
            return $this->sendError(__('message.sendmailerr'));
        }
    }
    //Validation du Code OTP
    /**
    * @OA\Post(
    *   path="/api/register/validotp",
    *   tags={"Register"},
    *   operationId="validotp",
    *   description="Validation du Code OTP",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"email", "otp", "lg"},
    *         @OA\Property(property="email", type="string"),
    *         @OA\Property(property="otp", type="string"),
    *         @OA\Property(property="lg", type="string")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Validation du Code OTP."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function validotp(Request $request): JsonResponse {
        //Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|size:6',
            'lg' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if($validator->fails()){
            Log::warning("Register - Validator otp : ".$request->email);
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        try {            
            // Récupérer les données
            $user = Checkotp::where([
                ['otp', $request->otp],
                ['email', $request->email],
            ])
            ->first();
            // Vérifier si les données existent
            if (!$user) {
                Log::warning("Code OTP erroné : " . $request->email);
                return $this->sendError("Code OTP erroné.", [], 404);
            }
            // Vérifier si l'OTP a expiré
            if (!($user->updated_at >= now()->subMinutes(10))) {
                Log::warning("Code OTP a expiré : " . $request->email);
                return $this->sendError("Code OTP a expiré.", [], 404);
            }
            return $this->sendSuccess("Code OTP validé avec succès.", [], 201);
        } catch(\Exception $e) {
            Log::warning("Une erreur est survenue, veuillez réessayer plus tard : " . $e->getMessage());
            return $this->sendError(__('message.error'));
        }
    }
    //Account creation
    /**
    * @OA\Post(
    *   path="/api/register/forms",
    *   tags={"Register"},
    *   operationId="registerUser",
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
			'photo' => 'required|file|mimes:png,jpeg,jpg|max:2048',
        ]);
		App::setLocale($request->lg);
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
        if (!($image->move($dir, $photo))) {
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
            // 'password_at' => now(),
            // 'password' => Hash::make($request->password),
        ];
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
            // Gender
            if ($request->gender == 'M')
                $gender = __('message.mr');
            else
                $gender = __('message.mrs');
            //subject
            $subject = __('message.creataccount');
            $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
            . __('message.dear') . " " . $gender . " " . $request->lastname . ",<br><br>"
            . __('message.txtaccount') . "<br><br>"
            . __('message.bestregard') . " !<br>
            <hr style='color:#156082;'>
            </div>";
            // Envoi de l'email
            $this->sendMail($email, '', $subject, $message);
            // Mail aux admins
            $admins = User::where('profile_id', 1)->get();
            foreach ($admins as $admin) :
                $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
                . __('message.dear') . " Admin,<br><br>"
                . __('message.txtadmin') . "<br><b>"
                . $gender . " " . $request->lastname . " " . $request->firstname
                . "</b><br><br>"
                . __('message.bestregard') . " !<br>
                <hr style='color:#156082;'>
                </div>";
                // Envoi de l'email
                $this->sendMail($admin->email, '', $subject, $message);
            endforeach;
            return $this->sendSuccess('Utilisateur enregistré avec succès.', $data, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::warning("User::store - Erreur enregistrement de l'utilisateur : " . $e->getMessage() . " " . json_encode($set));
            return $this->sendError("Erreur enregistrement de l'utilisateur");
        }
    }
}
