<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace;

use App\Models\SessionPayment;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConfirmSessionController extends Controller
{

    public function __invoke(\Illuminate\Http\Request $request, int $sessionId)
    {
        $validated = $request->validate([
            'meeting_link' => 'nullable|url',
        ]);

        $session = SessionPayment::where('id', $sessionId)
            ->where('mediator_id', Auth::id())
            ->firstOrFail();

        // Update metadata to mark as confirmed and save meeting link if provided
        $session->update([
            'meeting_link' => $validated['meeting_link'] ?? $session->meeting_link,
            'metadata' => array_merge($session->metadata ?? [], [
                'confirmed_by_mediator' => true,
                'confirmed_at' => now()->toIso8601String(),
            ]),
        ]);

        // Send confirmation email to the client
        $client = \App\Models\User::find($session->user_id);
        $mediator = Auth::user();
        
        if ($client && $client->email) {
            \Illuminate\Support\Facades\Mail::to($client->email)->send(
                new \App\Mail\SessionConfirmedByMediatorMail(
                    $client,
                    $mediator,
                    $session->scheduled_at,
                    $session->meeting_link
                )
            );
        }

        return back()->with('success', 'Sesión confirmada exitosamente. Se ha enviado un correo al cliente.');
    }
}
