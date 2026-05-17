<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;
use App\Models\AccountsReceivable;
use App\Models\Client;
use App\Models\ClientCustody;
use App\Models\ClientPaymentState;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Services\TwilioService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:backfill-client-history {--dry-run : Show the planned changes without writing them}', function () {
    $normalize = function (?string $value): string {
        return Str::of((string) $value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();
    };

    $summary = [
        'merged_clients' => 0,
        'reassigned_vehicles' => 0,
        'reassigned_work_orders' => 0,
        'reassigned_payment_states' => 0,
        'reassigned_receivables' => 0,
        'reassigned_custodies' => 0,
    ];

    DB::transaction(function () use ($normalize, &$summary) {
        $clients = Client::query()
            ->withCount(['vehicles', 'workOrders', 'paymentStates'])
            ->with(['vehicles', 'workOrders', 'paymentStates'])
            ->orderBy('id')
            ->get();

        $nameGroups = $clients->groupBy(fn (Client $client) => $normalize($client->nombre));

        foreach ($nameGroups as $groupKey => $group) {
            if ($groupKey === '' || $group->count() < 2) {
                continue;
            }

            $canonical = $group->sort(function (Client $left, Client $right) {
                $leftScore = (filled($left->telefono) ? 1000 : 0) + ($left->work_orders_count * 10) + ($left->vehicles_count * 5) + $left->payment_states_count;
                $rightScore = (filled($right->telefono) ? 1000 : 0) + ($right->work_orders_count * 10) + ($right->vehicles_count * 5) + $right->payment_states_count;

                if ($leftScore === $rightScore) {
                    return $left->id <=> $right->id;
                }

                return $rightScore <=> $leftScore;
            })->first();

            $duplicates = $group->reject(fn (Client $client) => $client->id === $canonical->id);
            if ($duplicates->isEmpty()) {
                continue;
            }

            foreach ($duplicates as $duplicate) {
                if (blank($canonical->telefono) && filled($duplicate->telefono)) {
                    $canonical->telefono = $duplicate->telefono;
                }

                if (blank($canonical->correo) && filled($duplicate->correo)) {
                    $canonical->correo = $duplicate->correo;
                }

                if ($this->option('dry-run')) {
                    $this->line(sprintf('Would merge client %s (%s) into %s (%s)', $duplicate->id, $duplicate->nombre, $canonical->id, $canonical->nombre));
                    continue;
                }

                if (filled($duplicate->telefono) || filled($duplicate->correo)) {
                    $canonical->fill(array_filter([
                        'telefono' => $canonical->telefono,
                        'correo' => $canonical->correo,
                    ], fn ($value) => $value !== null && $value !== ''));
                    $canonical->save();
                }

                $summary['reassigned_vehicles'] += Vehicle::where('client_id', $duplicate->id)->update(['client_id' => $canonical->id]);
                $summary['reassigned_work_orders'] += WorkOrder::where('client_id', $duplicate->id)->update(['client_id' => $canonical->id]);
                $summary['reassigned_payment_states'] += ClientPaymentState::where('client_id', $duplicate->id)->update(['client_id' => $canonical->id]);
                $summary['reassigned_receivables'] += AccountsReceivable::where('client_id', $duplicate->id)->update(['client_id' => $canonical->id]);
                $summary['reassigned_custodies'] += ClientCustody::where('client_id', $duplicate->id)->update(['client_id' => $canonical->id]);

                $duplicate->delete();
                $summary['merged_clients']++;
            }
        }

        if ($this->option('dry-run')) {
            return;
        }

        WorkOrder::query()
            ->with(['client', 'vehicle'])
            ->chunkById(100, function ($workOrders) use (&$summary) {
                foreach ($workOrders as $workOrder) {
                    if ($workOrder->vehicle && $workOrder->vehicle->client_id !== $workOrder->client_id) {
                        $workOrder->vehicle->update(['client_id' => $workOrder->client_id]);
                    }
                }
            }, 'id');
    });

    $this->info('Backfill client history finished.');
    foreach ($summary as $key => $value) {
        $this->line(str_replace('_', ' ', $key) . ': ' . $value);
    }
})->purpose('Merge duplicate clients and realign client history records');

Artisan::command('app:send-work-order-whatsapp-reminders', function (TwilioService $twilio) {
    $workOrders = WorkOrder::query()
        ->with(['client', 'vehicle'])
        ->whereDate('fecha_programada', today())
        ->whereHas('client', fn ($query) => $query->whereNotNull('telefono')->where('telefono', '!=', ''))
        ->where('status', '!=', 'Entregado')
        ->get();

    $sent = 0;

    foreach ($workOrders as $workOrder) {
        $twilio->notifyWorkOrderStatusOrDelivery($workOrder);
        $sent++;
    }

    $this->info("WhatsApp reminders sent: {$sent}");
})->purpose('Send WhatsApp reminders when a work order reaches its estimated delivery date');

Schedule::command('app:send-work-order-whatsapp-reminders')->dailyAt('08:00');
