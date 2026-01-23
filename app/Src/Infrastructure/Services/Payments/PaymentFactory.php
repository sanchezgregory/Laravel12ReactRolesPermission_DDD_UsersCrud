<?php

namespace App\Src\Infrastructure\Services\Payments;

use App\Src\Domain\Contracts\Payments\PaymentGatewayInterface;
use App\Src\Infrastructure\Services\Payments\Gateways\StripeGateway;
use App\Src\Infrastructure\Services\Payments\Gateways\MercadoPagoService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentFactory
{
    public function make(string $slug): PaymentGatewayInterface
    {
        $gateway = DB::table('payment_gateways')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
            
        if (!$gateway) {
            // Fallback for dev/local or if table not migrated yet? 
            // Better to fail safe.
            throw new InvalidArgumentException("Payment gateway '{$slug}' is not active or does not exist.");
        }
        
        $credentials = json_decode($gateway->credentials ?? '{}', true);

        return match ($slug) {
            'stripe' => new StripeGateway($credentials),
            'mercadopago' => new MercadoPagoService($credentials),
            default => throw new InvalidArgumentException("Gateway implementation not found for '{$slug}'"),
        };
    }
}
