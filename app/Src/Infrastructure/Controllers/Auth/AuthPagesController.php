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

    public function postLogin(Request $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->back()->with('status', 'Login successful');
        }

        return redirect()->back()->withErrors([
            'email' => 'Email or password is incorrect.',
        ]);
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

        return redirect()->back()->with('status', 'Register successful');
    }
}
