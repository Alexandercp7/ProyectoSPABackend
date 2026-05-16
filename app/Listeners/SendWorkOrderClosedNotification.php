<?php
namespace App\Listeners;
use App\Events\WorkOrderClosedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendWorkOrderClosedNotification implements ShouldQueue
{
    public function handle(WorkOrderClosedEvent $event): void
    {
        Log::info("OT cerrada: {$event->workOrder->id}. Notificacion pendiente de configuracion.");
    }
}
