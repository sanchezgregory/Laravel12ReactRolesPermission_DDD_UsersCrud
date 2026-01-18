<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace;

use App\Models\SessionPayment;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConfirmSessionController extends Controller
{
    public function __invoke(int $sessionId)
    {
        $session = SessionPayment::where('id', $sessionId)
            ->where('mediator_id', Auth::id())
            ->firstOrFail();

        // Update metadata to mark as confirmed
        $session->update([
            'metadata' => array_merge($session->metadata ?? [], [
                'confirmed_by_mediator' => true,
                'confirmed_at' => now()->toIso8601String(),
            ]),
        ]);

        return back()->with('success', 'Sesión confirmada exitosamente.');
    }
}
