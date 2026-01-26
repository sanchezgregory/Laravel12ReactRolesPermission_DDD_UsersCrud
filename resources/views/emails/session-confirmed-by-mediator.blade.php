<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Confirmada</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 3px solid #10B981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #10B981;
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            background-color: #F9FAFB;
            border-left: 4px solid #10B981;
            padding: 15px 20px;
            margin: 20px 0;
        }
        .info-label {
            font-weight: 600;
            color: #6B7280;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #111827;
            margin-bottom: 15px;
        }
        .info-value:last-child {
            margin-bottom: 0;
        }
        .button {
            display: inline-block;
            background-color: #10B981;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 14px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✅ Sesión Confirmada</h1>
        </div>

        <p>Hola <strong>{{ $clientName }}</strong>,</p>

        <p>Tu mediador, <strong>{{ $mediatorName }}</strong>, ha confirmado tu sesión. A continuación encontrarás todos los detalles y el enlace para unirte a la reunión.</p>

        <div class="info-section">
            <div class="info-label">Mediador</div>
            <div class="info-value">{{ $mediatorName }}</div>

            <div class="info-label">Fecha y Hora</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($scheduledAt)->format('d/m/Y H:i') }} (Hora Local)</div>

            @if($meetingLink)
            <div class="info-label">Enlace de Reunión</div>
            <div class="info-value">
                <a href="{{ $meetingLink }}" style="color: #10B981; word-break: break-all;">
                    {{ $meetingLink }}
                </a>
            </div>
            @endif
        </div>

        @if($meetingLink)
        <div style="text-align: center;">
            <a href="{{ $meetingLink }}" class="button">Unirse a la Reunión</a>
        </div>
        @endif

        <div class="footer">
            <p>Por favor, asegúrate de conectarte puntual a la hora acordada.</p>
            <p>Este es un correo automático. Si tienes alguna pregunta, por favor contacta a tu mediador o al equipo de soporte.</p>
        </div>
    </div>
</body>
</html>
