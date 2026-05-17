<!doctype html>
<html lang="es">
<body style="font-family: Arial, sans-serif; background: #f5f7fb; color: #1f2937; margin: 0; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);">
        <p style="margin: 0 0 12px; font-size: 12px; letter-spacing: .08em; text-transform: uppercase; color: #6b7280;">{{ config('app.name') }}</p>
        <h1 style="margin: 0 0 16px; font-size: 28px; line-height: 1.2; color: #111827;">{{ $headline }}</h1>
        <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.7; color: #374151; white-space: pre-line;">{{ $body }}</p>

        @if ($ctaUrl && $ctaLabel)
            <p style="margin: 28px 0 0;">
                <a href="{{ $ctaUrl }}" style="display: inline-block; background: #111827; color: #ffffff; text-decoration: none; padding: 12px 18px; border-radius: 999px; font-weight: 700;">
                    {{ $ctaLabel }}
                </a>
            </p>
        @endif
    </div>
</body>
</html>