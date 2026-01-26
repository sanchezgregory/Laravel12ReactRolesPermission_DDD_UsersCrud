<?php

namespace App\Src\Infrastructure\Controllers\Backoffice;

use App\Models\MediatorSession;
use App\Models\SessionPayment;
use App\Models\User;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        // 1. Top 5 Mediators (Most scheduled sessions)
        $topMediators = SessionPayment::select('mediator_id', DB::raw('count(*) as total'))
            ->where('status', 'paid')
            ->whereNotNull('mediator_id')
            ->groupBy('mediator_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('mediator')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->mediator ? $item->mediator->name : 'Unknown',
                    'sales' => $item->total,
                    // Revenue per mediator could be calculated here or in query, keeping it simple
                    'revenue' => 0, 
                    'trend' => 'up' // placeholder
                ];
            });

        // 2. Income Totals
        $totalIncomeMinor = SessionPayment::where('status', 'paid')->sum('amount_total');
        $platformIncomeMinor = $totalIncomeMinor * 0.30;
        
        $totalIncome = $totalIncomeMinor / 100;
        $platformIncome = $platformIncomeMinor / 100;

        // 3. Active Users (Logged in last 10 days)
        $activeUsers = User::where('last_login_at', '>=', now()->subDays(10))->count();

        // 4. Active Mediator Sessions
        $activeMediatorSessions = MediatorSession::where('is_active', true)->count();

        // 5. Income Distribution by Category
        $incomeDistribution = SessionPayment::where('status', 'paid')
            ->whereNotNull('mediator_session_id')
            ->with('mediatorSession')
            ->get()
            ->groupBy(function ($payment) {
                return $payment->mediatorSession ? $payment->mediatorSession->category : 'Uncategorized';
            })
            ->map(function ($payments, $category) {
                // Generate a consistent color based on category string
                $hash = md5($category);
                $color = '#' . substr($hash, 0, 6);
                
                return [
                    'name' => $category,
                    'value' => $payments->count(), // Or sum('amount_total')? Prompt says "percentage of each category of scheduled sessions". Can be count or volume. "porcentaje de cada categoría de las sessiones agendadas" -> Usually count.
                    'color' => $color
                ];
            })
            ->values();

        // 6. Recent Transactions
        $recentTransactions = SessionPayment::with(['user', 'mediator'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'customer' => $payment->user ? $payment->user->name : 'Unknown',
                    'email' => $payment->user ? $payment->user->email : '',
                    'mediator' => $payment->mediator ? $payment->mediator->name : 'Unknown',
                    'amount' => $payment->amount_total / 100,
                    'status' => $payment->status,
                    'date' => $payment->created_at->format('Y-m-d'),
                ];
            });
            
        // 7. Paid but Unconfirmed Transactions (Alerts)
        $pendingConfirmation = SessionPayment::where('status', 'paid')
            ->whereNull('confirmed_at')
            ->with(['user', 'mediator'])
            ->limit(10) // Limit to avoid displaying too many
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'customer' => $payment->user ? $payment->user->name : 'Unknown',
                    'mediator' => $payment->mediator ? $payment->mediator->name : 'Unknown',
                    'amount' => $payment->amount_total / 100,
                    'date' => $payment->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('dashboard', [
            'kpis' => [
               [
                   'title' => 'Ingresos Totales', 
                   'value' => '$' . number_format($totalIncome, 2), 
                   'icon' => 'DollarSign', 
                   'color' => 'text-green-600', 
                   'trend' => 'up', 
                   'change' => '+0%' // Placeholder
               ],
               [
                   'title' => 'Ingresos Plataforma (30%)', 
                   'value' => '$' . number_format($platformIncome, 2), 
                   'icon' => 'CreditCard', 
                   'color' => 'text-blue-600', 
                   'trend' => 'up', 
                   'change' => '+0%'
               ],
               [
                   'title' => 'Usuarios Activos (10d)', 
                   'value' => (string)$activeUsers, 
                   'icon' => 'Users', 
                   'color' => 'text-purple-600', 
                   'trend' => 'neutral', 
                   'change' => ''
               ],
               [
                   'title' => 'Sesiones Disponibles', 
                   'value' => (string)$activeMediatorSessions, 
                   'icon' => 'Activity', 
                   'color' => 'text-orange-600', 
                   'trend' => 'neutral', 
                   'change' => ''
               ],
            ],
            'topMediators' => $topMediators,
            'incomeDistribution' => $incomeDistribution,
            'recentTransactions' => $recentTransactions,
            'pendingConfirmation' => $pendingConfirmation,
        ]);
    }
}
