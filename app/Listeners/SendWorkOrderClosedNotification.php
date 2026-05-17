<?php
namespace App\Listeners;
use App\Events\WorkOrderClosedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Logs closed work orders until a richer notification channel is enabled.
 */
class SendWorkOrderClosedNotification implements ShouldQueue
{
    public function handle(WorkOrderClosedEvent $event): void
    {
        Log::info("OT cerrada: {$event->workOrder->id}. Notificacion pendiente de configuracion.");
    }
}
