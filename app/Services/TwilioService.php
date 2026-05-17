<?php
namespace App\Services;

use App\Mail\WorkOrderNotificationMail;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

/**
 * Sends client notifications for work-order events through WhatsApp and email.
 */
class TwilioService
{
    private ?Client $client = null;
    private string $from = '';
    private bool $enabled;

    public function __construct()
    {
        $sid    = config('services.twilio.sid');
        $token  = config('services.twilio.token');
        $this->from    = config('services.twilio.whatsapp_from') ?? '';
        $this->enabled = $sid && $token && $this->from;

        if ($this->enabled) {
            $this->client = new Client($sid, $token);
        }
    }

    public function notifyCreated(WorkOrder $wo): void
    {
        $phone = $wo->client?->telefono;
        if (!$phone) return;

        $portalUrl = config('app.frontend_url') . '/portal/' . $wo->portal_token;
        $vehiculo  = "{$wo->vehicle?->marca} {$wo->vehicle?->modelo} ({$wo->vehicle?->placas})";

        $this->send($phone,
            "✅ *{$wo->client->nombre}*, recibimos tu vehículo *{$vehiculo}*.\n\n" .
            "🔖 Orden: *{$wo->id}*\n" .
            "🔧 Problema reportado: {$wo->problema}\n\n" .
            "Sigue el estado de tu OT en tiempo real:\n{$portalUrl}"
        );

        $this->sendEmail(
            $wo,
            'Hemos recibido tu orden de trabajo',
            "Hola {$wo->client->nombre}:\n\nRecibimos tu vehículo {$vehiculo}.\nOrden: {$wo->id}\nProblema reportado: {$wo->problema}",
            'Ver portal',
            $portalUrl,
        );
    }

    public function notifyStatusChanged(WorkOrder $wo): void
    {
        $phone = $wo->client?->telefono;
        if (!$phone) return;

        $emoji = match ($wo->status) {
            'En Proceso'   => '🔧',
            'Terminado'    => '✅',
            'En Garantia'  => '🛡️',
            'Entregado'    => '🚗',
            default        => '📋',
        };

        $portalUrl = config('app.frontend_url') . '/portal/' . $wo->portal_token;

        $this->send($phone,
            "{$emoji} *{$wo->client->nombre}*, el estado de tu orden *{$wo->id}* cambió a: *{$wo->status}*.\n\n" .
            "Consulta los detalles aquí:\n{$portalUrl}"
        );

        $this->sendEmail(
            $wo,
            "Tu orden {$wo->id} cambió de estado",
            "Hola {$wo->client->nombre}:\n\nEl estado de tu orden {$wo->id} cambió a {$wo->status}.\nPuedes consultar el avance en el portal.",
            'Ver estado',
            $portalUrl,
        );
    }

    public function notifyDelivered(WorkOrder $wo): void
    {
        $phone = $wo->client?->telefono;
        if (!$phone) return;

        $vehiculo  = "{$wo->vehicle?->marca} {$wo->vehicle?->modelo}";
        $portalUrl = config('app.frontend_url') . '/portal/' . $wo->portal_token;

        $this->send($phone,
            "🚗 *{$wo->client->nombre}*, tu *{$vehiculo}* está listo para recoger.\n\n" .
            "🔖 Orden: *{$wo->id}*\n\n" .
            "Revisa el resumen completo de servicios:\n{$portalUrl}\n\n" .
            "¡Gracias por tu preferencia! 🙏"
        );

        $this->sendEmail(
            $wo,
            'Tu vehículo está listo para recoger',
            "Hola {$wo->client->nombre}:\n\nTu vehículo {$vehiculo} ya está listo para recoger.\nOrden: {$wo->id}\nGracias por confiar en nosotros.",
            'Revisar resumen',
            $portalUrl,
        );
    }

    private function sendEmail(WorkOrder $wo, string $subject, string $body, ?string $ctaLabel, ?string $ctaUrl): void
    {
        $email = $wo->client?->correo;
        if (!$email) {
            return;
        }

        try {
            Mail::to($email)->send(new WorkOrderNotificationMail(
                subjectLine: $subject,
                headline: $subject,
                body: $body,
                ctaLabel: $ctaLabel,
                ctaUrl: $ctaUrl,
            ));
        } catch (\Throwable $e) {
            Log::error('[Mail] Error sending work-order email to ' . $email . ': ' . $e->getMessage());
        }
    }

    private function send(string $phone, string $body): void
    {
        if (!$this->enabled) {
            Log::info('[Twilio disabled] WhatsApp to ' . $phone . ': ' . $body);
            return;
        }

        // Normalize to E.164 — assumes Mexico (+52) if no country code present
        $normalized = preg_replace('/\D/', '', $phone);
        if (strlen($normalized) === 10) {
            $normalized = '52' . $normalized;
        }
        $to = 'whatsapp:+' . $normalized;

        try {
            $this->client->messages->create($to, [
                'from' => 'whatsapp:' . $this->from,
                'body' => $body,
            ]);
        } catch (\Exception $e) {
            Log::error('[Twilio] Error sending WhatsApp to ' . $to . ': ' . $e->getMessage());
        }
    }
}
