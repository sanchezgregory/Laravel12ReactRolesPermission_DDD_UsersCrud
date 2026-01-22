<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use Illuminate\Http\Request;
use App\Src\Application\Services\PaymentConfigurationService;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Illuminate\Http\JsonResponse;

class StripeAccountController
{
    public function __construct(
        private PaymentConfigurationService $paymentConfigService
    ) {}

    /**
     * Create a Stripe Express Account for a mediator and generate an onboarding link.
     *
     * Steps:
     * 1. Validate the request (mediator_id is required).
     * 2. Initialize Stripe with the Secret Key.
     * 3. Create a Stripe Express Account using the mediator's email.
     * 4. Generate an Account Link for onboarding (this link allows the user to fill in their banking details).
     * 5. Persist the generated `account_id` in the database (MediatorFinancial) for future payments.
     * 6. Return the `account_id` and the `onboarding_url` to the admin.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        // 1. Validate Input
        $request->validate([
            'mediator_id' => 'required|integer',
            'email' => 'required|email',
        ]);

        $mediatorId = (int) $request->input('mediator_id');
        $email = $request->input('email');

        // 2. Set Stripe API Key
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // 3. Create Express Account
            // We use 'type' => 'express'.
            // Country is often required or inferred. For Express, usually strictly required or defaulted to platform's country.
            // We'll trust Stripe defaults or assume generic availability. If needed, 'app_settings' in Stripe Dashboard controls this.
            // Explicitly setting capabilities is good practice.
            $account = Account::create([
                'type' => 'express',
                'email' => $email,
                'country' => 'BR', // Default to BR, change if the platform is in another region or needs dynamic selection.
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                // 'settings' => [
                //     'payouts' => [
                //         'schedule' => [
                //             'interval' => 'manual', // Platform controls payouts? Or allow automatic 'daily' etc. Express usually allows user config.
                //         ],
                //     ],
                // ]
            ]);

            $accountId = $account->id;

            // 4. Create Account Link (Onboarding Link)
            // The Admin will send this URL to the Mediator.
            // When the Mediator completes (or exits), they are redirected.
            // Since the mediator might not be logged in or we want a generic landing page:
            $refreshUrl = config('app.url') . '/stripe/onboarding/refresh'; // Hypothetical route
            $returnUrl = config('app.url') . '/stripe/onboarding/complete'; // Hypothetical route

            $accountLink = AccountLink::create([
                'account' => $accountId,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);

            // 5. Persist Data
            // We need to store the account_id associated with the mediator.
            // We preserve existing data by fetching first.
            $currentFinancial = $this->paymentConfigService->getMediatorFinancial($mediatorId);
            
            $providersData = [];
            if ($currentFinancial && $currentFinancial->providersData) {
                $providersData = $currentFinancial->providersData;
            }

            // Update Stripe data
            // We store account_id. We might also want to store status (e.g., 'pending_onboarding') in the future.
            $providersData['stripe'] = [
                'account_id' => $accountId,
                'email' => $email
            ];

            $this->paymentConfigService->saveMediatorFinancial(
                $mediatorId,
                $currentFinancial ? $currentFinancial->customFeePercent : null,
                $providersData
            );

            // 6. Return Result
            return response()->json([
                'account_id' => $accountId,
                'url' => $accountLink->url,
                'message' => 'Stripe account created successfully. Share the URL with the mediator.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
