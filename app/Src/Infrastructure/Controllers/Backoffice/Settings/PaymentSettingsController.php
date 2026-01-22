<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Settings;

use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Application\Services\PaymentConfigurationService;
use App\Src\Application\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PaymentSettingsController extends Controller
{
    public function __construct(
        private readonly PaymentConfigurationService $paymentConfigService,
        private readonly UserService $userService
    ) {}

    public function index()
    {
        $globalFee = $this->paymentConfigService->getGlobalPlatformFeePercent();
        
        // We need mediators with their financial info.
        // UserService->getAll() might return just users or users with roles.
        // Let's assume we can filter by role 'mediator'.
        // For now, let's get all users and filter or use a specific method if available.
        // UserService has getAll() which uses UserRepository->getAll(). 
        // We'll iterate and attach financial info. This is N+1 but for admin settings it's fine for now.
        // Improving: create getAllMediatorsWithFinancials in Service.
        
        $users = $this->userService->getAll(); // Returns UserEntity[]
        
        $mediators = [];
        foreach ($users as $user) {
            // Check if user has mediator role. UserEntity usually has roles array.
            // Let's inspect UserEntity in a moment.
            // For now assuming we can filter.
            // Note: The UserEntity might just have 'roles' as array of strings.
             if (in_array('mediator', $user->roles ?? [])) {
                $financial = $this->paymentConfigService->getMediatorFinancial($user->id);
                $mediators[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'custom_fee_percent' => $financial?->customFeePercent,
                    'providers_data' => $financial?->providersData ?? [],
                ];
             }
        }

        return Inertia::render('settings/payments', [
            'globalFee' => $globalFee,
            'mediators' => $mediators,
        ]);
    }

    public function updateGlobal(Request $request)
    {
        $validated = $request->validate([
            'percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->paymentConfigService->updateGlobalPlatformFeePercent($validated['percent']);

        return back()->with('success', 'Global platform fee updated.');
    }

    public function updateMediator(Request $request, int $mediatorId)
    {
        $validated = $request->validate([
            'custom_fee_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'providers_data' => ['nullable', 'array'],
            'providers_data.stripe.account_id' => ['nullable', 'string'],
            'providers_data.paypal.email' => ['nullable', 'email'],
        ]);

        $this->paymentConfigService->saveMediatorFinancial(
            $mediatorId,
            $validated['custom_fee_percent'],
            $validated['providers_data']
        );

        return back()->with('success', 'Mediator payment settings updated.');
    }
}
