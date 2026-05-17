<?php
namespace App\UseCases\WorkOrders;

use App\Models\Client;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateWorkOrderUseCase
{
    public function execute(array $data, int $userId): WorkOrder
    {
        return DB::transaction(function () use ($data, $userId) {
            $clientName = trim((string) ($data['cliente_nombre'] ?? ''));
            $clientPhone = trim((string) ($data['cliente_telefono'] ?? ''));
            $clientEmail = $data['cliente_correo'] ?? null;

            // ── Resolve client ──────────────────────────────────────────────
            if (!empty($data['client_id'])) {
                $clientId = $data['client_id'];
            } else {
                $client = null;

                if ($clientPhone !== '') {
                    $client = Client::where('telefono', $clientPhone)->first();
                }

                if (!$client && $clientName !== '') {
                    $client = Client::whereRaw('LOWER(nombre) = ?', [mb_strtolower($clientName)])->first();
                }

                if (!$client) {
                    $client = Client::create([
                        'nombre' => $clientName !== '' ? $clientName : 'Cliente',
                        'telefono' => $clientPhone !== '' ? $clientPhone : null,
                        'correo' => $clientEmail,
                    ]);
                } elseif ($clientEmail && !$client->correo) {
                    $client->update(['correo' => $clientEmail]);
                }

                $clientId = $client->id;
            }

            // ── Resolve vehicle ─────────────────────────────────────────────
            if (!empty($data['vehicle_id'])) {
                $vehicleId = $data['vehicle_id'];
            } else {
                $placas = $data['vehiculo_placas'] ?? null;
                $vin    = $data['vehiculo_vin'] ?? null;

                $vehicle = null;

                if ($placas || $vin) {
                    $vehicle = Vehicle::where('client_id', $clientId)
                        ->when($placas, fn ($query) => $query->where('placas', $placas))
                        ->when($vin, fn ($query) => $query->orWhere('vin', $vin))
                        ->first();
                }

                if (!$vehicle && ($placas || $vin)) {
                    $vehicle = Vehicle::query()
                        ->when($placas, fn ($query) => $query->where('placas', $placas))
                        ->when($vin, fn ($query) => $query->orWhere('vin', $vin))
                        ->first();
                }

                if (!$vehicle) {
                    $vehicle = Vehicle::create([
                        'client_id'          => $clientId,
                        'marca'              => $data['vehiculo_marca']       ?? 'Pendiente',
                        'modelo'             => $data['vehiculo_modelo']      ?? 'Pendiente',
                        'anio'               => $data['vehiculo_anio']        ?? date('Y'),
                        'placas'             => $placas,
                        'vin'                => $vin,
                        'kilometraje_actual' => $data['vehiculo_kilometraje'] ?? 0,
                    ]);
                }

                $vehicleId = $vehicle->id;
            }

            // ── Generate WO ID ───────────────────────────────────────────────
            $lastId  = WorkOrder::orderByDesc('id')->value('id');
            $nextNum = 1;
            if ($lastId) {
                preg_match('/WO-(\d+)/', $lastId, $m);
                $nextNum = isset($m[1]) ? ((int)$m[1] + 1) : 1;
            }
            $id = 'WO-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // ── Create work order ────────────────────────────────────────────
            $wo = WorkOrder::create([
                'id'              => $id,
                'client_id'       => $clientId,
                'vehicle_id'      => $vehicleId,
                'tecnico_id'      => $data['tecnico_id']      ?? null,
                'status'          => 'Agendado',
                'priority'        => $data['priority']        ?? 'Media',
                'tipo_vehiculo'   => $data['tipo_vehiculo'],
                'problema'        => $data['problema'],
                'diagnostico'     => $data['diagnostico']     ?? null,
                'fecha_ingreso'   => $data['fecha_ingreso'],
                'fecha_programada'=> $data['fecha_programada'] ?? null,
                'portal_token'    => Str::uuid(),
            ]);

            $wo->timeline()->create([
                'descripcion' => 'Orden de trabajo creada.',
                'usuario_id'  => $userId,
            ]);

            return $wo->load(['client', 'vehicle', 'tecnico', 'timeline']);
        });
    }
}
