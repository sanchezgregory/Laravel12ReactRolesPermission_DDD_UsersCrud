<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Src\Application\DTO\Payments\CreateCheckoutDTO;
use App\Src\Domain\Contracts\ServiceContracts\SessionPaymentServiceInterface;
use App\Src\Infrastructure\Requests\Payments\CreateCheckoutSessionRequest;
use Illuminate\Http\JsonResponse;

class CreateCheckoutSessionController
{
    public function __construct(private readonly SessionPaymentServiceInterface $service) {}

    public function __invoke(CreateCheckoutSessionRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $dto = new CreateCheckoutDTO(
            userId: $data['user_id'],
            mediatorId: $data['mediator_id'],
            method: $data['method'],
            amountMinor: $data['amount_minor'],
            currency: $data['currency'],
            topic: $data['topic'],
            metadata: $data['metadata'],
        );

        $result = $this->service->createCheckout($dto);

        return response()->json([
            'payment_id' => $result->paymentId,
            'redirect_url' => $result->redirectUrl,
        ]);
    }
}
