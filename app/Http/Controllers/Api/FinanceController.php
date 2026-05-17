<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecordCashEntryRequest;
use App\Http\Requests\RegisterPaymentRequest;
use App\Http\Resources\AccountsPayableResource;
use App\Http\Resources\AccountsReceivableResource;
use App\Http\Resources\DailyCashEntryResource;
use App\Models\AccountsPayable;
use App\Models\AccountsReceivable;
use App\Models\DailyCashEntry;
use App\Models\WorkOrder;
use App\UseCases\Finance\RecordCashEntryUseCase;
use App\UseCases\Finance\RegisterPayablePaymentUseCase;
use App\UseCases\Finance\RegisterReceivablePaymentUseCase;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Exposes financial summaries, cash entries, receivables, and payables.
 */
class FinanceController extends Controller
{
    public function __construct(
        private RecordCashEntryUseCase $cashUC,
        private RegisterReceivablePaymentUseCase $rxUC,
        private RegisterPayablePaymentUseCase $pxUC,
    ) {}

    public function summary()
    {
        $now   = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end   = $now->copy()->endOfMonth();

        $ingresos = DailyCashEntry::where('tipo','Ingreso')
            ->whereBetween('fecha',[$start->toDateString(),$end->toDateString()])
            ->sum('monto');

        $gastos = DailyCashEntry::where('tipo','Egreso')
            ->whereBetween('fecha',[$start->toDateString(),$end->toDateString()])
            ->sum('monto');

        $cxcPendiente = AccountsReceivable::where('estado','!=','Pagado')->sum('monto_pendiente');

        $totalCxc = AccountsReceivable::count();
        $pagadasCxc = AccountsReceivable::where('estado','Pagado')->count();
        $tasaCobranza = $totalCxc > 0 ? round(($pagadasCxc / $totalCxc) * 100, 1) : 0;

        return response()->json(['data' => [
            'total_ingresos'  => $ingresos,
            'total_gastos'    => $gastos,
            'cxc_pendiente'   => $cxcPendiente,
            'tasa_cobranza'   => $tasaCobranza,
            'mes'             => $now->translatedFormat('F Y'),
        ]]);
    }

    public function comparative()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m     = Carbon::now()->subMonths($i);
            $start = $m->copy()->startOfMonth()->toDateString();
            $end   = $m->copy()->endOfMonth()->toDateString();

            $months[] = [
                'mes'      => $m->format('Y-m'),
                'label'    => $m->translatedFormat('M Y'),
                'ingresos' => DailyCashEntry::where('tipo','Ingreso')->whereBetween('fecha',[$start,$end])->sum('monto'),
                'egresos'  => DailyCashEntry::where('tipo','Egreso')->whereBetween('fecha',[$start,$end])->sum('monto'),
                'ots'      => WorkOrder::whereDate('created_at','>=',$start)->whereDate('created_at','<=',$end)->count(),
            ];
        }
        return response()->json(['data' => $months]);
    }

    public function cashIndex(Request $request)
    {
        $entries = DailyCashEntry::when($request->fecha, fn($q) => $q->where('fecha', $request->fecha))
            ->orderByDesc('created_at')
            ->paginate(50);

        $totales = [
            'ingreso' => DailyCashEntry::where('tipo','Ingreso')
                ->when($request->fecha, fn($q) => $q->where('fecha', $request->fecha))
                ->sum('monto'),
            'egreso' => DailyCashEntry::where('tipo','Egreso')
                ->when($request->fecha, fn($q) => $q->where('fecha', $request->fecha))
                ->sum('monto'),
        ];

        return DailyCashEntryResource::collection($entries)->additional(['meta' => ['totales' => $totales]]);
    }

    public function cashStore(RecordCashEntryRequest $request)
    {
        $entry = $this->cashUC->execute($request->validated(), $request->user()->id);
        return response()->json(['data' => new DailyCashEntryResource($entry)], 201);
    }

    public function cashDestroy(int $id)
    {
        $entry = DailyCashEntry::findOrFail($id);
        if ($entry->fecha !== now()->toDateString()) {
            return response()->json(['message' => 'Solo se pueden eliminar entradas del dia actual.'], 422);
        }
        $entry->delete();
        return response()->json(['message' => 'Entrada eliminada.']);
    }

    public function receivableIndex(Request $request)
    {
        $cxc = AccountsReceivable::with('client')
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
            ->orderByDesc('created_at')
            ->paginate(20);
        return AccountsReceivableResource::collection($cxc);
    }

    public function receivablePayment(RegisterPaymentRequest $request, int $id)
    {
        $cxc = $this->rxUC->execute($id, (float)$request->monto, $request->metodo_pago, $request->user()->id);
        return response()->json(['data' => new AccountsReceivableResource($cxc)]);
    }

    public function payableIndex(Request $request)
    {
        $cxp = AccountsPayable::with('contact')
            ->when($request->estado, fn($q) => $q->where('estado', $request->estado))
            ->orderByDesc('created_at')
            ->paginate(20);
        return AccountsPayableResource::collection($cxp);
    }

    public function payablePayment(RegisterPaymentRequest $request, int $id)
    {
        $cxp = $this->pxUC->execute($id, (float)$request->monto, $request->metodo_pago, $request->user()->id);
        return response()->json(['data' => new AccountsPayableResource($cxp)]);
    }

    public function reports(string $type)
    {
        return response()->json(['message' => "Reporte '$type' encolado para generacion."], 202);
    }
}
