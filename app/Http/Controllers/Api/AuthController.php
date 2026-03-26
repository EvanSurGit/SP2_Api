<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['Identifiants incorrects.'],
        ]);
    }

    $user = Auth::user();

    // 👇 On bloque les non-admins dès le login
    if (!$user->isAdmin()) {
        Auth::logout();
        return response()->json([
            'message' => 'Accès refusé. Réservé aux administrateurs.'
        ], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message'      => 'Connexion réussie.',
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'user'         => [
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ],
    ]);
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}