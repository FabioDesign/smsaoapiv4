<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\{Country, Nationality};
use Illuminate\Support\Facades\{App, Log};
use App\Http\Controllers\API\BaseController as BaseController;

class SettingController extends BaseController
{
    // Nationality
    /**
    * @OA\Get(
    *   path="/api/settings/nationality",
    *   tags={"Settings"},
    *   operationId="nationality",
    *   description="Liste de nationalités.",
    *   @OA\Response(response=200, description="Liste de nationalités."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function nationality($lg): JsonResponse
    {
        App::setLocale($lg);
        $nationality = Nationality::select('id', $lg . ' as label')->get();
        return $this->sendSuccess(__('message.listnation'), $nationality);
    }

    // LListe de pays
    /**
    * @OA\Get(
    *   path="/api/settings/country",
    *   tags={"Settings"},
    *   operationId="country",
    *   description="Liste de pays.",
    *   @OA\Response(response=200, description="Liste de pays."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function country($lg): JsonResponse
    {
        App::setLocale($lg);
        $country = Country::select('id', $lg . ' as label', 'alpha', 'code')
        ->where('status', 1)
        ->get();
        return $this->sendSuccess(__('message.listcountry'), $country);
    }
}