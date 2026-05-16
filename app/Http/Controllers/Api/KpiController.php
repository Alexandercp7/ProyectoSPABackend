<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\KpiResource;
use App\Models\Kpi;
use App\Models\KpiActivity;
use App\Models\OrganizationInfo;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        return KpiResource::collection(Kpi::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string',
            'role_responsable' => 'required|string',
            'meta'             => 'required|integer',
            'periodo'          => 'required|in:semanal,mensual,trimestral,anual',
            'fecha_inicio'     => 'required|date',
            'fecha_fin'        => 'required|date',
        ]);
        $kpi = Kpi::create($request->validated());
        return response()->json(['data' => new KpiResource($kpi)], 201);
    }

    public function update(Request $request, int $id)
    {
        $kpi = Kpi::findOrFail($id);
        $kpi->update($request->only(['nombre','descripcion','role_responsable','meta','periodo','fecha_inicio','fecha_fin']));
        return response()->json(['data' => new KpiResource($kpi)]);
    }

    public function kpiActivitiesIndex(Request $request)
    {
        $items = KpiActivity::with('employee')
            ->when($request->role_asignado, fn($q) => $q->where('role_asignado', $request->role_asignado))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();
        return response()->json(['data' => $items]);
    }

    public function kpiActivitiesStore(Request $request)
    {
        $request->validate([
            'titulo'           => 'required|string',
            'role_asignado'    => 'required|string',
            'prioridad'        => 'nullable|in:Alta,Media,Baja',
            'empleado_id'      => 'nullable|exists:employees,id',
            'fecha_vencimiento'=> 'nullable|date',
        ]);
        $item = KpiActivity::create($request->validated());
        return response()->json(['data' => $item], 201);
    }

    public function kpiActivitiesUpdate(Request $request, int $id)
    {
        $item = KpiActivity::findOrFail($id);
        $item->update($request->only(['titulo','descripcion','status','prioridad','empleado_id','fecha_vencimiento']));
        return response()->json(['data' => $item]);
    }

    public function kpiActivitiesDestroy(int $id)
    {
        KpiActivity::findOrFail($id)->delete();
        return response()->json(['message' => 'Actividad KPI eliminada.']);
    }

    public function organizationInfo()
    {
        $info = OrganizationInfo::firstOrCreate(['id' => 1]);
        return response()->json(['data' => $info]);
    }

    public function updateOrganizationInfo(Request $request)
    {
        $request->validate([
            'mision' => 'nullable|string',
            'vision' => 'nullable|string',
            'valores'=> 'nullable|array',
        ]);
        $info = OrganizationInfo::firstOrCreate(['id' => 1]);
        $info->update($request->only(['mision','vision','valores']));
        return response()->json(['data' => $info]);
    }
}
