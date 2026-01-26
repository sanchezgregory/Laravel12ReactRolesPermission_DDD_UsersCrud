<?php

namespace App\Src\Infrastructure\Controllers\Auth;

use App\Models\User;
use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthPagesController extends Controller
{
    public function login(): Response
    {
        return Inertia::render('public_auth/Login');
    }

    public function register(): Response
    {
        return Inertia::render('public_auth/Register');
    }

    public function forgotPassword(): Response
    {
        return Inertia::render('public_auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function resetPassword(Request $request, string $token): Response
    {
        return Inertia::render('public_auth/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function postLogin(\App\Src\Infrastructure\Requests\Auth\LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        if ($user) {
            $user->update(['last_login_at' => now()]);

            if ($user->hasAnyRole(['admin', 'mediator'])) {
                return redirect()->intended(route('backoffice.dashboard'));
            }

            // Default for end users
            return redirect()->intended(route('user.sessions'));
        }

        return redirect()->back(); // Fallback, though authenticate() throws if failed
    }

    public function postRegister(Request $request): RedirectResponse
    {
            $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $user->update(['last_login_at' => now()]);

        return redirect()->route('user.sessions')->with('status', 'Register successful');
    }
}
