<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WorkOrderShareMail;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Exposes the public portal used by clients to track work-order progress.
 */
class ClientPortalController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $wo = WorkOrder::where('portal_token', $token)
            ->with([
                'client:id,nombre,telefono',
                'vehicle:id,marca,modelo,anio,placas,kilometraje_actual',
                'tecnico:id,name',
                'timeline:id,work_order_id,descripcion,created_at',
                'checklists:id,work_order_id,tipo,tarea,completada,orden',
                'photos:id,work_order_id,url',
                'parts:id,work_order_id,cantidad,costo_unitario,inventory_item_id',
                'parts.inventoryItem:id,nombre',
                'services:id,work_order_id,price_item_id',
                'services.priceItem:id,concepto,precio_auto,precio_camioneta,precio_camion',
            ])
            ->firstOrFail();

        // Filter out internal notes — client only sees public ones
        $publicNotes = $wo->notes()
            ->where('tipo', 'cliente')
            ->orderBy('created_at')
            ->get(['id','texto','created_at']);

        $checklistProgress = [
            'total'      => $wo->checklists->count(),
            'completadas'=> $wo->checklists->where('completada', true)->count(),
        ];

        $servicios = $wo->services->map(fn($s) => [
            'nombre' => $s->priceItem?->concepto,
            'precio' => match ($wo->tipo_vehiculo) {
                'Camioneta' => $s->priceItem?->precio_camioneta,
                'Camion'    => $s->priceItem?->precio_camion,
                default     => $s->priceItem?->precio_auto,
            },
        ]);

        $refacciones = $wo->parts->map(fn($p) => [
            'nombre'   => $p->inventoryItem?->nombre,
            'cantidad' => $p->cantidad,
            'subtotal' => $p->cantidad * $p->costo_unitario,
        ]);

        return response()->json([
            'data' => [
                'id'              => $wo->id,
                'status'          => $wo->status,
                'priority'        => $wo->priority,
                'tipo_vehiculo'   => $wo->tipo_vehiculo,
                'problema'        => $wo->problema,
                'diagnostico'     => $wo->diagnostico,
                'fecha_ingreso'   => $wo->fecha_ingreso,
                'fecha_programada'=> $wo->fecha_programada,
                'cliente'         => $wo->client,
                'vehiculo'        => $wo->vehicle,
                'tecnico'         => $wo->tecnico ? ['nombre' => $wo->tecnico->name] : null,
                'timeline'        => $wo->timeline,
                'checklist'       => $checklistProgress,
                'fotos'           => $wo->photos->pluck('url'),
                'notas'           => $publicNotes,
                'servicios'       => $servicios,
                'refacciones'     => $refacciones,
                'total_estimado'  => $servicios->sum('precio') + $refacciones->sum('subtotal'),
            ],
        ]);
    }

    /** Regenera el token (staff only — ruta protegida) */
    public function regenerateToken(string $id): JsonResponse
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->update(['portal_token' => Str::uuid()]);

        return response()->json([
            'data' => [
                'portal_token' => $wo->portal_token,
                'portal_url'   => config('app.frontend_url') . '/portal/' . $wo->portal_token,
            ],
        ]);
    }

    public function shareByEmail(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'correo' => 'required|email',
        ]);

        $wo = WorkOrder::with('client')->findOrFail($id);
        $portalUrl = config('app.frontend_url') . '/portal/' . $wo->portal_token;
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($portalUrl);
        $recipient = $request->correo;

        Mail::to($recipient)->send(new WorkOrderShareMail(
            orderId: $wo->id,
            portalUrl: $portalUrl,
            qrUrl: $qrUrl,
            clientName: $wo->client?->nombre ?? 'cliente',
        ));

        return response()->json([
            'message' => 'Correo enviado correctamente.',
            'data' => [
                'sent_to' => $recipient,
                'portal_url' => $portalUrl,
            ],
        ]);
    }
}
