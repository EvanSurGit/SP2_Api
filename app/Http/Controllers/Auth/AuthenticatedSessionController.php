<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str; 

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        // URL précédente (HTTP Referer)
        $previous = url()->previous();
        $current  = url()->current(); // l’URL de /logout
        $fallback = route('home');    // ex: '/'
    
        // Pages qui nécessitent d'être connecté
        $protectedPrefixes = ['/panier', '/checkout', '/dashboard', '/profile'];
    
        // Choix de la destination après logout
        $to = $fallback;
        if ($previous && $previous !== $current) {
            $path = parse_url($previous, PHP_URL_PATH) ?? '/';
            $isProtected = Str::startsWith($path, $protectedPrefixes);
            $to = $isProtected ? $fallback : $previous;
        }

    // Déconnexion
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->to($to);
}
}
