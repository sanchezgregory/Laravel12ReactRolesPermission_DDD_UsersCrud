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
    public function __invoke(): Response|\Illuminate\Http\RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Check if user is strictly a mediator (and not an admin who sees everything)
        $isMediator = $user->hasRole('mediator') && !$user->hasRole('admin');
        
        // Redirect standard users to their sessions page
        if ($user->hasRole('user') && !$user->hasRole('admin') && !$user->hasRole('mediator')) {
            return redirect()->route('user.sessions');
        }

        // 1. Top 5 Mediators (Most scheduled sessions)
        $topMediatorsQuery = SessionPayment::select('mediator_id', DB::raw('count(*) as total'))
            ->where('status', 'paid')
            ->whereNotNull('mediator_id')
            ->groupBy('mediator_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('mediator');

        if ($isMediator) {
            $topMediatorsQuery->where('mediator_id', $user->id);
        }

        $topMediators = $topMediatorsQuery->get()
            ->map(function ($item) {
                return [
                    'name' => $item->mediator ? $item->mediator->name : 'Unknown',
                    'sales' => $item->total,
                    'revenue' => 0, 
                    'trend' => 'up'
                ];
            });

        // 2. Income Totals
        $incomeQuery = SessionPayment::where('status', 'paid');
        if ($isMediator) {
            $incomeQuery->where('mediator_id', $user->id);
        }
        $totalIncomeMinor = $incomeQuery->sum('amount_total');
        
        $platformIncomeMinor = $totalIncomeMinor * 0.30;
        
        // For mediators, their income is 70% of the total (minus platform fee)
        // For admins, "Ingresos Totales" usually means GMV (Gross Merchandise Volume), and Platform Income is their cut.
        if ($isMediator) {
            // Mediator sees their Net Income (70%)
            $totalIncome = ($totalIncomeMinor * 0.70) / 100;
        } else {
            // Admin sees GMV
            $totalIncome = $totalIncomeMinor / 100;
        }
        
        $platformIncome = $platformIncomeMinor / 100;

        // 3. Active Users / Clients
        if ($isMediator) {
            // "My Clients" - Users who have booked this mediator
            $activeUsers = SessionPayment::where('mediator_id', $user->id)
                ->distinct('user_id')
                ->count('user_id');
        } else {
            // "Active Users" - All users logged in recently
            $activeUsers = User::where('last_login_at', '>=', now()->subDays(10))->count();
        }

        // 4. Active Mediator Sessions
        $activeSessionsQuery = MediatorSession::where('is_active', true);
        if ($isMediator) {
            $activeSessionsQuery->where('mediator_id', $user->id);
        }
        $activeMediatorSessions = $activeSessionsQuery->count();

        // 5. Income Distribution by Category
        $distQuery = SessionPayment::where('status', 'paid')
            ->whereNotNull('mediator_session_id')
            ->with('mediatorSession');

        if ($isMediator) {
            $distQuery->where('mediator_id', $user->id);
        }

        $incomeDistribution = $distQuery->get()
            ->groupBy(function ($payment) {
                return $payment->mediatorSession ? $payment->mediatorSession->category : 'Uncategorized';
            })
            ->map(function ($payments, $category) {
                $hash = md5($category);
                $color = '#' . substr($hash, 0, 6);
                
                return [
                    'name' => $category,
                    'value' => $payments->count(),
                    'color' => $color
                ];
            })
            ->values();

        // 6. Recent Transactions
        $transactionsQuery = SessionPayment::with(['user', 'mediator'])
            ->latest();
            
        if ($isMediator) {
            $transactionsQuery->where('mediator_id', $user->id);
        }

        $recentTransactions = $transactionsQuery->limit(5)
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
        $pendingQuery = SessionPayment::where('status', 'paid')
            ->whereNull('confirmed_at')
            ->with(['user', 'mediator']);

        if ($isMediator) {
            $pendingQuery->where('mediator_id', $user->id);
        }

        $pendingConfirmation = $pendingQuery->limit(10)
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

        // KPI Configurations
        $kpiTitleIncome = 'Ingresos Totales (GMV)';
        $kpiValueIncome = number_format($totalIncome, 2);
        
        $kpiTitleSecondary = 'Ingresos Plataforma (30%)';
        $kpiValueSecondary = number_format($platformIncome, 2);

        $kpiTitleUsers = 'Usuarios Activos (10d)';

        if ($isMediator) {
            $kpiTitleIncome = 'Mis Ingresos (70%)';
            // For mediator, secondary KPI could be "Total Sales Volume" or maybe hidden?
            // Let's keep it as "Ventas Totales" (GMV) or "Comisión Plataforma"
            $kpiTitleSecondary = 'Comisión Plataforma (30%)';
            $kpiTitleUsers = 'Mis Clientes';
        }

        return Inertia::render('dashboard', [
            'kpis' => [
               [
                   'title' => $kpiTitleIncome, 
                   'value' => '$' . $kpiValueIncome, 
                   'icon' => 'DollarSign', 
                   'color' => 'text-green-600', 
                   'trend' => 'up', 
                   'change' => '+0%' 
               ],
               [
                   'title' => $kpiTitleSecondary, 
                   'value' => '$' . $kpiValueSecondary, 
                   'icon' => 'CreditCard', 
                   'color' => 'text-blue-600', 
                   'trend' => 'up', 
                   'change' => '+0%'
               ],
               [
                   'title' => $kpiTitleUsers, 
                   'value' => (string)$activeUsers, 
                   'icon' => 'Users', 
                   'color' => 'text-purple-600', 
                   'trend' => 'neutral', 
                   'change' => ''
               ],
               [
                   'title' => $isMediator ? 'Mis Sesiones Activas' : 'Sesiones Disponibles', 
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
