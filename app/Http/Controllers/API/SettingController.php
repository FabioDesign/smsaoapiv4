<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{App, Log};
use App\Models\{Cells, Country, District, Nationality, Province, Sector};
use App\Http\Controllers\API\BaseController as BaseController;

class SettingController extends BaseController
{
    // Liste de Nationalités
    /**
    * @OA\Get(
    *   path="/api/settings/nationality/{lg}",
    *   tags={"Settings"},
    *   operationId="nationality",
    *   description="Liste de Nationalités.",
    *   @OA\Response(response=200, description="Liste de Nationalités."),
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
    *   path="/api/settings/country/{lg}",
    *   tags={"Settings"},
    *   operationId="country",
    *   description="Liste de Pays.",
    *   @OA\Response(response=200, description="Liste de Pays."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function country($lg): JsonResponse
    {
        App::setLocale($lg);
        $country = Country::select('id', 'code', 'alpha', $lg . ' as label')
        ->where('status', 1)
        ->get();
        return $this->sendSuccess(__('message.listcountry'), $country);
    }
    
    // Liste de provinces
    /**
    * @OA\Get(
    *   path="/api/settings/province/{lg}",
    *   tags={"Settings"},
    *   operationId="province",
    *   description="Liste de provinces.",
    *   @OA\Response(response=200, description="Liste de provinces."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function province($lg): JsonResponse
    {
        App::setLocale($lg);
        $province = Province::select('id', 'code', $lg . ' as label')->get();
        return $this->sendSuccess(__('message.listprovince'), $province);
    }

    // Liste de régions
    /**
    * @OA\Get(
    *   path="/api/settings/region/{lg}/{country_id}",
    *   tags={"Settings"},
    *   operationId="region",
    *   description="Liste de régions.",
    *   @OA\Response(response=200, description="Liste de régions."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function region($lg, $country_id): JsonResponse
    {
        App::setLocale($lg);
        $regions = Province::select('id', 'code', $lg . ' as label')
        ->where('country_id', $country_id)
        ->get();
        return $this->sendSuccess(__('message.listregion'), $regions);
    }

    // Liste de districts
    /**
    * @OA\Get(
    *   path="/api/settings/district/{lg}/{province_id}",
    *   tags={"Settings"},
    *   operationId="district",
    *   description="Liste de districts.",
    *   @OA\Response(response=200, description="Liste de districts."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function district($lg, $province_id): JsonResponse
    {
        App::setLocale($lg);
        $districts = District::select('id', 'code', 'label')
        ->where('province_id', $province_id)
        ->get();
        return $this->sendSuccess(__('message.listdistrict'), $districts);
    }

    // Liste de sectors
    /**
    * @OA\Get(
    *   path="/api/settings/sector/{lg}/{district_id}",
    *   tags={"Settings"},
    *   operationId="sector",
    *   description="Liste de sectors.",
    *   @OA\Response(response=200, description="Liste de sectors."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function sector($lg, $district_id): JsonResponse
    {
        App::setLocale($lg);
        $sectors = Sector::select('id', 'label')
        ->where('district_id', $district_id)
        ->where('status', 1)
        ->get();
        return $this->sendSuccess(__('message.listsector'), $sectors);
    }

    // Liste de cells
    /**
    * @OA\Get(
    *   path="/api/settings/cells/{lg}/{sector_id}",
    *   tags={"Settings"},
    *   operationId="cells",
    *   description="Liste de cells.",
    *   @OA\Response(response=200, description="Liste de cells."),
    *   @OA\Response(response=401, description="Aucune donnée trouvée."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function cells($lg, $sector_id): JsonResponse
    {
        App::setLocale($lg);
        $cells = Cells::select('id', 'label')
        ->where('sector_id', $sector_id)
        ->where('status', 1)
        ->get();
        return $this->sendSuccess(__('message.listcells'), $cells);
    }

}