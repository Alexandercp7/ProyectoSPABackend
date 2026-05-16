<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with('client')
            ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
            ->get();
        return response()->json(['data' => $vehicles]);
    }

    public function store(CreateVehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->validated());
        return response()->json(['data' => $vehicle->load('client')], 201);
    }

    public function update(Request $request, int $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($request->only(['marca','modelo','anio','placas','vin','kilometraje_actual']));
        return response()->json(['data' => $vehicle]);
    }

    public function destroy(int $id)
    {
        Vehicle::findOrFail($id)->delete();
        return response()->json(['message' => 'Vehiculo eliminado.']);
    }
}
