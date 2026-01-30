<?php

namespace App\Src\Infrastructure\Controllers\Payments;

use App\Mail\SessionScheduledConfirmationMail;
use App\Src\Application\Services\SessionPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\SessionPayment;
use App\Models\User;

class SubmitScheduledSessionController extends Controller
{
    public function __construct(private readonly SessionPaymentService $sessionPaymentService)
    {
    }

    /**
     * Handle user-submitted schedule information after they manually booked in Calendly.
     * Expected POST data: mediator_id, scheduled_at (datetime), notes (optional)
     */
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'mediator_id' => 'required|integer|exists:users,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'participants' => 'nullable|array|max:5',
            'participants.*.email' => 'required|email',
        ]);

        $userId = Auth::id();
        $mediatorId = (int) $validated['mediator_id'];
        $scheduledAt = $validated['scheduled_at'];
        $notes = $validated['notes'] ?? null;
        $participants = $validated['participants'] ?? [];

        // Find the active paid session for this user and mediator
        $session = SessionPayment::where('user_id', $userId)
            ->where('mediator_id', $mediatorId)
            ->where('status', 'paid')
            ->whereNull('scheduled_at') // Only update if not already scheduled
            ->first();

        if (!$session) {
            return back()->withErrors(['error' => 'No se encontró una sesión pagada pendiente de agendar.']);
        }

        // Save participants
        if (!empty($participants)) {
            foreach ($participants as $participant) {
                $session->participants()->create([
                    'email' => $participant['email'],
                ]);
            }
        }

        // Update the scheduled_at and notes
        $session->update([
            'scheduled_at' => $scheduledAt,
            'metadata' => array_merge($session->metadata ?? [], [
                'user_notes' => $notes,
                'scheduled_by_user_at' => now()->toIso8601String(),
            ]),
        ]);

        // Get mediator info
        $mediator = User::find($mediatorId);
        $user = Auth::user();

        // Send email to the mediator via event
        if ($mediator) {
             \App\Events\SessionScheduled::dispatch(
                $mediator,
                $user,
                $scheduledAt,
                $notes,
                array_column($participants, 'email') // Pass participant emails
            );
        }

        // Redirect back with success message
        return back()->with('success', 'Tu sesión ha sido registrada. El mediador y los participantes recibirán un correo de confirmación.');
    }
}

