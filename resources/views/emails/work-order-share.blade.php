<!doctype html>
<html lang="es">
<body style="font-family: Arial, sans-serif; background: #f5f7fb; color: #1f2937; margin: 0; padding: 24px;">
    <div style="max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);">
        <p style="margin: 0 0 12px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: #6b7280;">{{ config('app.name') }}</p>
        <h1 style="margin: 0 0 12px; font-size: 28px; line-height: 1.2; color: #111827;">Seguimiento de tu orden {{ $orderId }}</h1>
        <p style="margin: 0 0 18px; font-size: 16px; line-height: 1.7; color: #374151;">
            Hola {{ $clientName }}, aquí tienes el acceso para revisar el avance de tu unidad.
        </p>

        <div style="display: flex; gap: 24px; align-items: center; flex-wrap: wrap; margin: 24px 0;">
            <div style="padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; background: #fafafa;">
                <img src="{{ $qrUrl }}" alt="QR para la orden {{ $orderId }}" width="220" height="220" style="display: block; border-radius: 8px;" />
            </div>
            <div style="min-width: 260px; flex: 1;">
                <p style="margin: 0 0 10px; font-size: 14px; color: #6b7280;">También puedes abrir el enlace directo:</p>
                <a href="{{ $portalUrl }}" style="word-break: break-all; color: #111827; font-weight: 700; text-decoration: none;">{{ $portalUrl }}</a>
                <p style="margin: 18px 0 0; font-size: 14px; line-height: 1.6; color: #4b5563;">
                    Escanea el QR o abre el enlace para ver el estado actual, fotos, checklist y avance de la reparación.
                </p>
            </div>
        </div>
    </div>
</body>
</html>