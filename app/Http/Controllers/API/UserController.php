<?php
namespace App\Http\Controllers\API; 

use \Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
use App\Models\{Parents, Permission, Profile, User};
use Illuminate\Support\Facades\{App, Auth, DB, Log, Validator};
use App\Http\Controllers\API\BaseController as BaseController;

/**
 * @OA\Info(title="API RWANDA", version="1.0")
 */
class UserController extends BaseController
{
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
        }catch(\Exception $e) {
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