<?php

namespace App\Http\Controllers\API;


use App\Models\{Checkotp, User};
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{App, Auth, Hash, Log, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

class PasswordController extends BaseController
{
    //Vérification de l'email
    /**
    * @OA\Post(
    *   path="/api/password/verifemail",
    *   tags={"Password"},
    *   operationId="verifemail",
    *   description="Vérification de l'email",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"email", "lg"},
    *         @OA\Property(property="email", type="string", example="fabio@yopmail.com"),
    *         @OA\Property(property="lg", type="string")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Vérification de l'email."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function verifemail(Request $request): JsonResponse {
        //Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'lg' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if($validator->fails()){
            Log::warning("Password forgot - Validator email : ".$request->email);
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        try {
            // Récupérer les données
            $user = User::where('email', $request->email)->first();
            // Générer l'OTP sécurisé
            $otp = random_int(100, 999) . ' ' . random_int(100, 999);
            // Gender
            if ($user->gender == 'M')
                $gender = __('message.mr');
            else
                $gender = __('message.mrs');
            //subject
            $subject = __('message.forgotpwd');
            $message = "<div style='color:#156082;font-size:11pt;line-height:1.5em;font-family:Century Gothic'>"
            . __('message.dear') . " " . $gender ." ".$user->lastname.",<br><br>"
            . __('message.otp') . " : <b>" . $otp . "</b><br><br>"
            . __('message.bestregard') . " !<br>
            <hr style='color:#156082;'>
            </div>";
            try {
                // Envoi de l'email
                $this->sendMail($request->email, '', $subject, $message);
                // Mettre à jour l'utilisateur avec l'OTP et l'horodatage
                $user->update([
                    'otp' => str_replace(' ', '', $otp),
                    'otp_at' => now(),
                ]);
                return $this->sendSuccess(__('message.sendmailsucc'), [], 201);
            } catch(\Exception $e) {
                Log::warning("Erreur d'envoi de mail : " . $e->getMessage());
                return $this->sendError(__('message.sendmailerr'));
            }
        } catch(\Exception $e) {
            Log::warning("Erreur de récupération de l'utilisateur : " . $e->getMessage());
            return $this->sendError(__('message.error'));
        }
    }
    //Vérification du Code OTP
    /**
    * @OA\Post(
    *   path="/api/password/verifotp",
    *   tags={"Password"},
    *   operationId="verifotp",
    *   description="Vérification du Code OTP",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"email", "otp", "lg"},
    *         @OA\Property(property="email", type="string"),
    *         @OA\Property(property="otp", type="string"),
    *         @OA\Property(property="lg", type="string")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Vérification du Code OTP."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function verifotp(Request $request): JsonResponse {
        //Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|size:6',
            'lg' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if($validator->fails()){
            Log::warning("Password forgot - Validator otp : ".$request->email);
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        try {
            // Récupérer les données
            $user = User::where([
                ['otp', $request->otp],
                ['email', $request->email],
            ])
            ->first();
            // Vérifier si les données existent
            if (!$user) {
                Log::warning("Email ou Code OTP erroné : " . $request->email);
                return $this->sendError("Email ou Code OTP erroné.", [], 404);
            }
            // Vérifier si l'OTP a expiré
            if (!($user->otp_at >= now()->subMinutes(5))) {
                Log::warning("Code OTP a expiré : " . $request->email);
                return $this->sendError("Code OTP a expiré.", [], 404);
            }
            return $this->sendSuccess("Code OTP validé avec succès.", [], 201);
        } catch(\Exception $e) {
            Log::warning("Une erreur est survenue, veuillez réessayer plus tard : " . $e->getMessage());
            return $this->sendError(__('message.error'));
        }
    }
    //Réinitialisation de Mot de passe
    /**
    * @OA\Post(
    *   path="/api/password/addpass",
    *   tags={"Password"},
    *   operationId="addpass",
    *   description="Ajout de Mot de passe",
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"email", "otp", "password", "password_confirmation", "lg"},
    *         @OA\Property(property="email", type="string"),
    *         @OA\Property(property="otp", type="string"),
    *         @OA\Property(property="password", type="string", format="password"),
    *         @OA\Property(property="password_confirmation", type="string", format="password"),
    *         @OA\Property(property="lg", type="string")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Mot de passe modifié avec succès."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function addpass(Request $request){
        //Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|size:6',
            'password' => [
                'required', 'confirmed',
                Password::min(8)
                    ->mixedCase() // Majuscules + minuscules
                    ->letters()   // Doit contenir des lettres
                    ->numbers()   // Doit contenir des chiffres
                    ->symbols()   // Doit contenir des caractères spéciaux
            ],
            'lg' => 'required',
        ]);
		App::setLocale($request->lg);
        //Error field
        if($validator->fails()){
            Log::warning("Validator password forgot - password : " . json_encode($request->all()));
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        // Récupérer les données
        $user = User::where([
            ['otp', $request->otp],
            ['email', $request->email],
        ])
        ->first();
        // Vérifier si les données existent
        if (!$user) {
            Log::warning("Email ou Code OTP erroné : " . $request->email);
            return $this->sendError("Email ou Code OTP erroné.", [], 404);
        }
        // Vérifier si l'OTP a expiré
        if (!($user->otp_at >= now()->subMinutes(10))) {
            Log::warning("Code OTP a expiré : " . $request->email);
            return $this->sendError("Code OTP a expiré.", [], 404);
        }
        try {
            // Mettre à jour du password
            $user->update([
                'password_at' => now(),
                'password' => Hash::make($request->password),
            ]);
            return $this->sendSuccess("Mot de passe modifié avec succès.", [], 201);
        } catch(\Exception $e) {
            Log::warning("Une erreur est survenue, veuillez réessayer plus tard : " . $e->getMessage());
            return $this->sendError("Une erreur est survenue, veuillez réessayer plus tard.");
        }
    }
    //Modification de Mot de passe
    /**
    * @OA\Post(
    *   path="/api/password/editpass",
    *   tags={"Password"},
    *   operationId="editpass",
    *   description="Modification de Mot de passe",
    *   security={{"bearer":{}}},
    *   @OA\RequestBody(
    *      required=true,
    *      @OA\JsonContent(
    *         required={"oldpass", "password", "password_confirmation"},
    *         @OA\Property(property="oldpass", type="string", format="password"),
    *         @OA\Property(property="password", type="string", format="password"),
    *         @OA\Property(property="password_confirmation", type="string", format="password")
    *      )
    *   ),
    *   @OA\Response(response=200, description="Mot de passe modifié avec succès."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function editpass(Request $request){
        //User
        $user = Auth::user();
		App::setLocale($user->lg);
        //Data
        Log::notice("ID Utilisateur : {$user->id} - Requête : " . json_encode($request->all()));
        //Validator
        $validator = Validator::make($request->all(), [
            'oldpass' => 'required|min:8',
            'password' => [
                'required', 'confirmed', 'different:oldpass',
                Password::min(8)
                    ->mixedCase() // Majuscules + minuscules
                    ->letters()   // Doit contenir des lettres
                    ->numbers()   // Doit contenir des chiffres
                    ->symbols()   // Doit contenir des caractères spéciaux
            ],
        ]);
        //Error field
        if($validator->fails()){
            Log::warning("Validator password edit : " . json_encode($request->all()));
            return $this->sendSuccess('Champs invalides.', $validator->errors(), 422);
        }
        // Vérification de l'ancien mot de passe
        if (!Hash::check($request->oldpass, $user->password)) {
            Log::warning("Ancien mot de passe incorrect pour l'utilisateur ID : {$user->id}");
            return $this->sendError("Ancien mot de passe incorrect.");
        }
        try {
            // Mettre à jour du password
            User::findOrFail($user->id)->update([
                'password_at' => now(),
                'password' => Hash::make($request->password),
            ]);
            return $this->sendSuccess("Mot de passe modifié avec succès.", [], 201);
        } catch(\Exception $e) {
            Log::warning("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
            return $this->sendError("Une erreur est survenue, veuillez réessayer plus tard.");
        }
    }
}
