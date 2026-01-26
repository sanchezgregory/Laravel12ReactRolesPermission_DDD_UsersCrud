<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace;

use App\Mail\SessionRescheduledNotificationMail;
use App\Models\SessionPayment;
use App\Models\User;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UpdateSessionScheduleController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|integer|exists:session_payments,id',
            'scheduled_at' => 'required|date',
            'meeting_link' => 'nullable|url',
        ]);

        $session = SessionPayment::where('id', $validated['session_id'])
            ->where('mediator_id', Auth::id())
            ->firstOrFail();

        $oldScheduledAt = $session->scheduled_at;

        // Update the scheduled_at
        $session->update([
            'scheduled_at' => $validated['scheduled_at'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'metadata' => array_merge($session->metadata ?? [], [
                'rescheduled_by_mediator' => true,
                'rescheduled_at' => now()->toIso8601String(),
                'previous_scheduled_at' => $oldScheduledAt,
            ]),
        ]);

        // Get client info
        $client = User::find($session->user_id);
        $mediator = Auth::user();

        // Send email to the client via event
        if ($client) {
             \App\Events\SessionRescheduled::dispatch(
                $client,
                $mediator,
                $validated['scheduled_at'],
                $oldScheduledAt
            );
        }

        return back()->with('success', 'Fecha y hora actualizadas. El cliente ha sido notificado por email.');
    }
}
