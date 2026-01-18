<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio en la Fecha de tu Sesión</title>
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
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            color: #4F46E5;
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            background-color: #F9FAFB;
            border-left: 4px solid #4F46E5;
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
        .old-date {
            text-decoration: line-through;
            color: #9CA3AF;
        }
        .new-date {
            color: #059669;
            font-weight: 600;
            font-size: 18px;
        }
        .alert-box {
            background-color: #DBEAFE;
            border: 1px solid #3B82F6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box strong {
            color: #1E40AF;
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
            <h1>🔄 Cambio en la Fecha de tu Sesión</h1>
        </div>

        <p>Hola <strong>{{ $clientName }}</strong>,</p>

        <p>Tu mediador <strong>{{ $mediatorName }}</strong> ha modificado la fecha y hora de tu sesión de mediación.</p>

        <div class="info-section">
            <div class="info-label">Mediador</div>
            <div class="info-value">{{ $mediatorName }} ({{ $mediatorEmail }})</div>

            @if($oldScheduledAt)
            <div class="info-label">Fecha Anterior</div>
            <div class="info-value old-date">{{ \Carbon\Carbon::parse($oldScheduledAt)->format('d/m/Y H:i') }}</div>
            @endif

            <div class="info-label">Nueva Fecha y Hora</div>
            <div class="info-value new-date">{{ \Carbon\Carbon::parse($newScheduledAt)->format('d/m/Y H:i') }}</div>
        </div>

        <div class="alert-box">
            <strong>ℹ️ Importante:</strong> Por favor, asegúrate de que esta nueva fecha y hora sean convenientes para ti. Si tienes algún inconveniente, contacta directamente con tu mediador.
        </div>

        <div class="footer">
            <p>Este es un correo automático. Si tienes alguna pregunta, por favor contacta directamente con tu mediador.</p>
        </div>
    </div>
</body>
</html>
