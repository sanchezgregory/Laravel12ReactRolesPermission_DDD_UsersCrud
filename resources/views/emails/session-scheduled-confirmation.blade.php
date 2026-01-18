<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Sesión Agendada</title>
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
        .alert-box {
            background-color: #FEF3C7;
            border: 1px solid #FCD34D;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-box strong {
            color: #92400E;
        }
        .button {
            display: inline-block;
            background-color: #4F46E5;
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
            <h1>📅 Nueva Sesión Agendada</h1>
        </div>

        <p>Hola <strong>{{ $mediatorName }}</strong>,</p>

        <p>Un cliente ha registrado una nueva sesión contigo. Por favor, <strong>verifica en tu Calendly</strong> que la fecha y hora coincidan con lo indicado a continuación:</p>

        <div class="info-section">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $userName }} ({{ $userEmail }})</div>

            <div class="info-label">Fecha y Hora Indicada</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($scheduledAt)->format('d/m/Y H:i') }}</div>

            @if($notes)
            <div class="info-label">Notas del Cliente</div>
            <div class="info-value">{{ $notes }}</div>
            @endif
        </div>

        <div class="alert-box">
            <strong>⚠️ Acción Requerida:</strong> Por favor, verifica en tu Calendly que esta cita esté correctamente registrada. Si todo coincide, podrás confirmar la sesión desde tu panel de mediador.
        </div>

        @if($calendlyUrl)
        <a href="{{ $calendlyUrl }}" class="button">Ver mi Calendly</a>
        @endif

        <div class="footer">
            <p>Este es un correo automático. Si tienes alguna pregunta, por favor contacta al equipo de soporte.</p>
        </div>
    </div>
</body>
</html>
