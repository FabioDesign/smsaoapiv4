<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{App, Log};
use App\Models\{Cells, Country, District, Nationality, Period, Province, Sector};
use App\Http\Controllers\API\BaseController as BaseController;

class ListsController extends BaseController
{
    // Liste de Nationalités
    /**
    * @OA\Get(
    *   path="/api/nationality/list/{lg}",
    *   tags={"Lists"},
    *   operationId="nationality",
    *   description="Liste de Nationalités.",
    *   @OA\Response(response=200, description="Liste de Nationalités."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function nationality($lg): JsonResponse {
		App::setLocale($lg);
        try {
            $nationality = Nationality::select('id', $lg . ' as label')->get();
            // Vérifier si les données existent
            if ($nationality->isEmpty()) {
                Log::warning("List::nationality - Aucune nationalité trouvée.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listnation'), $nationality);
        } catch (\Exception $e) {
            Log::warning("List::nationality - Erreur lors de la récupération des nationalités: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des nationalités.");
        }
    }

    // LListe de pays
    /**
    * @OA\Get(
    *   path="/api/country/list/{lg}",
    *   tags={"Lists"},
    *   operationId="country",
    *   description="Liste de Pays.",
    *   @OA\Response(response=200, description="Liste de Pays."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function country($lg): JsonResponse {
		App::setLocale($lg);
        try {
            $country = Country::select('id', 'code', 'alpha', $lg . ' as label')
            ->where('status', 1)
            ->get();
            // Vérifier si les données existent
            if ($country->isEmpty()) {
                Log::warning("List::country - Aucun pays trouvé.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listcountry'), $country);
        } catch (\Exception $e) {
            Log::warning("List::country - Erreur lors de la récupération des pays: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des pays.");
        }
    }
    
    // Liste de provinces
    /**
    * @OA\Get(
    *   path="/api/provinces/list/{lg}",
    *   tags={"Lists"},
    *   operationId="province",
    *   description="Liste de provinces.",
    *   @OA\Response(response=200, description="Liste de provinces."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function provinces($lg): JsonResponse {
		App::setLocale($lg);
        try {
            $provinces = Province::select('id', 'code', $lg . ' as label')->get();
            // Vérifier si les données existent
            if ($provinces->isEmpty()) {
                Log::warning("List::provinces - Aucun province trouvé.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listprovince'), $provinces);
        } catch (\Exception $e) {
            Log::warning("List::provinces - Erreur lors de la récupération des provinces: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des provinces.");
        }
    }

    // Liste de régions
    /**
    * @OA\Get(
    *   path="/api/regions/list/{lg}/{country_id}",
    *   tags={"Lists"},
    *   operationId="region",
    *   description="Liste de régions.",
    *   @OA\Response(response=200, description="Liste de régions."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function regions($lg, $country_id): JsonResponse {
		App::setLocale($lg);
        try {
            $regions = Province::select('id', 'code', $lg . ' as label')
            ->where('country_id', $country_id)
            ->get();
            // Vérifier si les données existent
            if ($regions->isEmpty()) {
                Log::warning("List::regions - Aucune region trouvée.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listregion'), $regions);
        } catch (\Exception $e) {
            Log::warning("Menu::index - Erreur lors de la récupération des regions: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des regions.");
        }
    }

    // Liste de districts
    /**
    * @OA\Get(
    *   path="/api/districts/list/{lg}/{province_id}",
    *   tags={"Lists"},
    *   operationId="district",
    *   description="Liste de districts.",
    *   @OA\Response(response=200, description="Liste de districts."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function districts($lg, $province_id): JsonResponse {
		App::setLocale($lg);
        try {
            $districts = District::select('id', 'code', 'label')
            ->where('province_id', $province_id)
            ->get();
            // Vérifier si les données existent
            if ($districts->isEmpty()) {
                Log::warning("List::districts - Aucun district trouvé.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listdistrict'), $districts);
        } catch (\Exception $e) {
            Log::warning("List::districts - Erreur lors de la récupération des districts: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des districts.");
        }
    }

    // Liste de secteurs
    /**
    * @OA\Get(
    *   path="/api/sectors/list/{lg}/{district_id}",
    *   tags={"Lists"},
    *   operationId="sector",
    *   description="Liste de secteurs.",
    *   @OA\Response(response=200, description="Liste de secteurs."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function sectors($lg, $district_id): JsonResponse {
		App::setLocale($lg);
        try {
            $sectors = Sector::select('id', 'label')
            ->where('district_id', $district_id)
            ->where('status', 1)
            ->get();
            // Vérifier si les données existent
            if ($sectors->isEmpty()) {
                Log::warning("List::sectors - Aucun secteur trouvé.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listector'), $sectors);
        } catch (\Exception $e) {
            Log::warning("List::sectors - Erreur lors de la récupération des secteurs: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des secteurs.");
        }
    }

    // Liste de cellules
    /**
    * @OA\Get(
    *   path="/api/cells/list/{lg}/{sector_id}",
    *   tags={"Lists"},
    *   operationId="cells",
    *   description="Liste de cellules.",
    *   @OA\Response(response=200, description="Liste de cellules."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function cells($lg, $sector_id): JsonResponse {
		App::setLocale($lg);
        try {
            $cells = Cells::select('id', 'label')
            ->where('sector_id', $sector_id)
            ->where('status', 1)
            ->get();
            // Vérifier si les données existent
            if ($cells->isEmpty()) {
                Log::warning("List::cells - Aucune cellule trouvée.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess(__('message.listcells'), $cells);
        } catch (\Exception $e) {
            Log::warning("List::cells - Erreur lors de la récupération des cellules: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des cellules.");
        }
    }
    //Liste des menus
    /**
    * @OA\Get(
    *   path="/api/menus/list/{lg}",
    *   tags={"Lists"},
    *   operationId="listMenu",
    *   description="Liste des menus",
    *   @OA\Response(response=200, description="Liste des menus."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function menus($lg): JsonResponse {
		App::setLocale($lg);
        try {
            // Code to list menus
            $menus = Menu::select('id', $lg . ' as label')->get();
            // Vérifier si les données existent
            if ($menus->isEmpty()) {
                Log::warning("List::menus - Aucun menu trouvé.");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess("Liste des menus.", $menus);
        } catch (\Exception $e) {
            Log::warning("List::menus - Erreur lors de la récupération des menus: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des menus.");
        }
    }
    //Liste des actions
    /**
    * @OA\Get(
    *   path="/api/actions/list/{lg}",
    *   tags={"Lists"},
    *   operationId="listAction",
    *   description="Liste des actions",
    *   @OA\Response(response=200, description="Liste des actions."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function actions($lg): JsonResponse {
		App::setLocale($lg);
        try {
            // Code to list actions
            $actions = Action::select('id', $lg . ' as label')->get();
            // Vérifier si les données existent
            if ($actions->isEmpty()) {
                Log::warning("List::actions - Aucune action trouvée");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess("Liste des actions.", $actions);
        } catch (\Exception $e) {
            Log::warning("List::actions - Erreur lors de la récupération des actions: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des actions.");
        }
    }
    //Liste des périodes
    /**
    * @OA\Get(
    *   path="/api/periods/list/{lg}",
    *   tags={"Lists"},
    *   operationId="listPeriod",
    *   description="Liste des périodes",
    *   @OA\Response(response=200, description="Liste des périodes."),
    *   @OA\Response(response=400, description="Bad Request."),
    *   @OA\Response(response=404, description="Page introuvable.")
    * )
    */
    public function periods($lg): JsonResponse {
		App::setLocale($lg);
        try {
            // Code to list periods
            $periods = Period::select('id', $lg . ' as label')->get();
            // Vérifier si les données existent
            if ($periods->isEmpty()) {
                Log::warning("List::periods - Aucune période trouvée");
                return $this->sendSuccess("Aucune donnée trouvée.");
            }
            return $this->sendSuccess("Liste des périodes.", $periods);
        } catch (\Exception $e) {
            Log::warning("List::periods - Erreur lors de la récupération des périodes: " . $e->getMessage());
            return $this->sendError("Erreur lors de la récupération des périodes.");
        }
    }
}