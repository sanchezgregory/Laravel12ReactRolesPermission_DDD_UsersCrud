<?php

namespace App\Src\Infrastructure\Controllers\Api;

use App\Src\Infrastructure\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Ya que attempt() fue exitoso, obtenemos el usuario directamente.
        $user = User::where('email', $request->email)->first();

        if (!$user->hasAnyRole(['user'])) {
            Auth::guard('api')->logout();
            return response()->json(['error' => 'Access Denied: Your account does not have the necessary permissions.'], 403);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('user');

        $token = Auth::guard('api')->attempt($request->only('email', 'password'));

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }

    public function refresh()
    {
        if (! $token = Auth::guard('api')->refresh()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
}
