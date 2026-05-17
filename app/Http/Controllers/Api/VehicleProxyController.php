<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

/**
 * Proxies vehicle lookup requests for external integration points.
 */
class VehicleProxyController extends Controller
{
    private const NHTSA_BASE = 'https://vpic.nhtsa.dot.gov/api/vehicles';

    public function makes()
    {
        $data = Cache::remember('nhtsa_makes', 86400, function () {
            $res = Http::get(self::NHTSA_BASE . '/GetAllMakes?format=json');
            return $res->json();
        });
        return response()->json(['data' => $data]);
    }

    public function models(string $make)
    {
        $key  = 'nhtsa_models_' . strtolower($make);
        $data = Cache::remember($key, 86400, function () use ($make) {
            $res = Http::get(self::NHTSA_BASE . "/GetModelsForMake/{$make}?format=json");
            return $res->json();
        });
        return response()->json(['data' => $data]);
    }

    public function decodeVin(string $vin)
    {
        $key  = 'nhtsa_vin_' . strtoupper($vin);
        $data = Cache::rememberForever($key, function () use ($vin) {
            $res = Http::get(self::NHTSA_BASE . "/DecodeVin/{$vin}?format=json");
            return $res->json();
        });
        return response()->json(['data' => $data]);
    }

    public function recalls(Request $request)
    {
        $make  = $request->make ?? '';
        $model = $request->model ?? '';
        $year  = $request->year ?? '';
        $key   = "nhtsa_recalls_{$make}_{$model}_{$year}";

        $data = Cache::remember($key, 43200, function () use ($make, $model, $year) {
            $res = Http::get("https://api.nhtsa.gov/recalls/recallsByVehicle", [
                'make' => $make, 'model' => $model, 'modelYear' => $year,
            ]);
            return $res->json();
        });
        return response()->json(['data' => $data]);
    }
}
