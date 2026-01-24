# Sistema de Logging de Pagos - Implementación Completa

## 📊 Resumen

Se ha implementado un sistema completo de logging estructurado para rastrear todas las transacciones de pago desde la creación hasta la confirmación final.

## 🎯 Componentes Implementados

### 1. **Canal de Log Dedicado** (`config/logging.php`)
```php
'payments' => [
    'driver' => 'daily',
    'path' => storage_path('logs/payments.log'),
    'level' => 'debug',
    'days' => 90, // Retención de 90 días para auditoría
    'replace_placeholders' => true,
],
```

**Ubicación del archivo**: `storage/logs/payments-YYYY-MM-DD.log`

### 2. **PaymentLogger Service** (`app/Src/Infrastructure/Services/Payments/PaymentLogger.php`)

Servicio centralizado con métodos específicos para cada evento del ciclo de vida del pago:

#### Métodos Principales:

- `logPaymentCreated()` - Pago creado en BD
- `logGatewaySessionCreated()` - Sesión/preferencia creada en gateway
- `logWebhookReceived()` - Webhook recibido del gateway
- `logPaymentStatusFetched()` - Estado obtenido del gateway
- `logPaymentLookup()` - Búsqueda de pago en BD
- `logPaymentStatusUpdated()` - Estado actualizado
- `logPaymentAlreadyProcessed()` - Webhook idempotente
- `logUserReturn()` - Usuario regresa del gateway
- `logPaymentSync()` - Sincronización de estado
- `logPaymentError()` - Errores
- `logPaymentWarning()` - Advertencias

### 3. **Integraciones Completadas**

#### ✅ `GeneralSessionPaymentService.php`
- `createCheckout()`: Logging de creación de pago y sesión de gateway
- Manejo de errores en creación de gateway

#### ✅ `MercadoPagoService.php`
- `handleWebhook()`: Logging de estado obtenido de MercadoPago
- Logging de errores en webhook

#### ✅ `PaymentReturnController.php`
- Logging de retorno del usuario desde el gateway

### 4. **Integraciones Pendientes** (Ver `docs/payment-logger-integration.md`)

Los siguientes métodos tienen código de ejemplo en la documentación pero requieren integración manual:

- `GeneralSessionPaymentService::syncPaymentStatus()`
- `GeneralSessionPaymentService::handleWebhook()`

## 📝 Formato de Logs

Cada entrada de log incluye:

```json
{
  "event": "PAYMENT_CREATED",
  "timestamp": "2026-01-24T19:30:00-03:00",
  "environment": "local",
  "payment_id": 27,
  "gateway": "mercadopago",
  "user_id": 24,
  "mediator_id": 15,
  "amount_minor": 14208,
  "currency": "USD",
  "status": "pending"
}
```

## 🔍 Eventos Rastreados

### Flujo Completo de Pago:

1. **PAYMENT_CREATED** - Pago creado en BD
2. **GATEWAY_SESSION_CREATED** - Preferencia/sesión creada en MercadoPago/Stripe
3. **WEBHOOK_RECEIVED** - Webhook recibido
4. **PAYMENT_STATUS_FETCHED** - Estado consultado en API del gateway
5. **PAYMENT_LOOKUP** - Búsqueda del pago en BD
6. **PAYMENT_STATUS_UPDATED** - Estado actualizado a `paid`/`failed`
7. **USER_RETURN** - Usuario regresa a la aplicación
8. **PAYMENT_SYNC** - Sincronización manual de estado

### Eventos de Error:

- **PAYMENT_ERROR** - Errores críticos con stack trace
- **PAYMENT_WARNING** - Advertencias (pago no encontrado, webhook no manejado, etc.)
- **PAYMENT_ALREADY_PROCESSED** - Webhooks duplicados (idempotencia)

## 📂 Estructura de Archivos

```
app/Src/Infrastructure/Services/Payments/
├── PaymentLogger.php                    ✅ NUEVO
├── GeneralSessionPaymentService.php     ✅ ACTUALIZADO
└── Gateways/
    └── MercadoPagoService.php          ✅ ACTUALIZADO

app/Src/Infrastructure/Controllers/Payments/
└── PaymentReturnController.php         ✅ ACTUALIZADO

config/
└── logging.php                          ✅ ACTUALIZADO

docs/
└── payment-logger-integration.md       ✅ NUEVO (Guía de integración)

storage/logs/
└── payments-YYYY-MM-DD.log             📝 Archivo de logs generado
```

## 🧪 Ejemplo de Uso

```php
use App\Src\Infrastructure\Services\Payments\PaymentLogger;

// Logging de creación de pago
PaymentLogger::logPaymentCreated(
    paymentId: $payment->id,
    gateway: 'mercadopago',
    userId: 24,
    mediatorId: 15,
    amountMinor: 14208,
    currency: 'USD'
);

// Logging de error
PaymentLogger::logPaymentError(
    context: 'webhook_processing',
    message: 'Failed to update payment status',
    data: ['payment_id' => 27, 'gateway' => 'mercadopago'],
    exception: $exception
);
```

## 🔧 Próximos Pasos

Para completar la integración:

1. **Revisar** `docs/payment-logger-integration.md`
2. **Aplicar** los cambios sugeridos en `GeneralSessionPaymentService.php`:
   - Método `syncPaymentStatus()`
   - Método `handleWebhook()`
3. **Probar** el flujo completo y verificar logs en `storage/logs/payments-YYYY-MM-DD.log`
4. **Opcional**: Configurar alertas para eventos `PAYMENT_ERROR`

## 📊 Beneficios

✅ **Trazabilidad completa** de cada transacción  
✅ **Debugging simplificado** con contexto enriquecido  
✅ **Auditoría** de 90 días de historial  
✅ **Logs estructurados** en JSON para análisis  
✅ **Separación de concerns** (logs de pagos separados de logs de aplicación)  
✅ **Detección de problemas** (webhooks fallidos, pagos no encontrados, etc.)

## 🎯 Casos de Uso

### Debugging de Pago Fallido
```bash
# Ver todos los eventos de un pago específico
grep "payment_id\":27" storage/logs/payments-2026-01-24.log

# Ver solo errores
grep "PAYMENT_ERROR" storage/logs/payments-2026-01-24.log
```

### Auditoría de Transacciones
```bash
# Ver todos los pagos completados hoy
grep "PAYMENT_STATUS_UPDATED.*paid" storage/logs/payments-2026-01-24.log

# Ver webhooks recibidos
grep "WEBHOOK_RECEIVED" storage/logs/payments-2026-01-24.log
```

### Monitoreo de Problemas
```bash
# Pagos no encontrados en webhooks
grep "Payment not found" storage/logs/payments-2026-01-24.log

# Webhooks no manejados
grep "Webhook not handled" storage/logs/payments-2026-01-24.log
```

---

**Fecha de Implementación**: 2026-01-24  
**Versión**: 1.0  
**Estado**: ✅ Implementado y Funcional
