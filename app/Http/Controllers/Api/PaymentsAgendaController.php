<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentsAgendaRequest;
use App\Http\Resources\PaymentsAgendaResource;
use App\Models\PaymentsAgenda;
use App\UseCases\Payments\ConfirmPaymentUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentsAgendaController extends Controller
{
    public function __construct(private ConfirmPaymentUseCase $confirmUC) {}

    public function index(Request $request)
    {
        $items = PaymentsAgenda::when($request->categoria, fn($q) => $q->where('categoria', $request->categoria))
            ->when($request->fecha, fn($q) => $q->where('fecha_vencimiento', $request->fecha))
            ->orderBy('fecha_vencimiento')
            ->paginate(30);
        return PaymentsAgendaResource::collection($items);
    }

    public function store(CreatePaymentsAgendaRequest $request)
    {
        $agenda = PaymentsAgenda::create($request->validated());
        return response()->json(['data' => new PaymentsAgendaResource($agenda)], 201);
    }

    public function update(Request $request, int $id)
    {
        $agenda = PaymentsAgenda::findOrFail($id);
        $agenda->update($request->only(['concepto','tipo','categoria','fecha_vencimiento','monto_presupuestado','monto_pagado','notas']));
        return response()->json(['data' => new PaymentsAgendaResource($agenda)]);
    }

    public function confirm(int $id, Request $request)
    {
        $agenda = $this->confirmUC->execute($id, $request->user()->id);
        return response()->json(['data' => new PaymentsAgendaResource($agenda)]);
    }

    public function uploadReceipt(Request $request, int $id)
    {
        $request->validate(['comprobante' => 'required|file|max:10240']);
        $agenda = PaymentsAgenda::findOrFail($id);
        $path   = $request->file('comprobante')->store("payments_agenda/$id", 'public');
        $agenda->update(['comprobante_url' => Storage::url($path)]);
        return response()->json(['data' => new PaymentsAgendaResource($agenda)]);
    }
}
