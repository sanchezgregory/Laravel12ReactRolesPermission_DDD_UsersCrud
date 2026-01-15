<?php

namespace App\Src\Infrastructure\Controllers\Backoffice\Auth;

use App\Src\Infrastructure\Controllers\Controller;
use App\Src\Infrastructure\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $user = Auth::user();

        // Verificamos que el usuario tenga al menos el rol 'admin' o 'mediator'
        if (!$user->hasAnyRole(['admin', 'mediator'])) {
            Auth::logout(); // Cerramos la sesión recién creada

            // Devolvemos un error de validación personalizado
            throw ValidationException::withMessages([
                'email' => 'No tienes los permisos necesarios para acceder.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('backoffice.dashboard', absolute: false))->with('success', 'Login successful');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
