<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Deny login for deactivated accounts (audit trail preserved)
        if ($user->status === 'nonaktif') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator sistem.',
            ]);
        }

        $request->session()->regenerate();

        // Record last login timestamp
        $user->update(['last_login' => now()]);

        // Pengurus Kandang → mobile PK dashboard
        if ($user->role === 'pengurus_kandang') {
            return redirect()->route('pk.dashboard');
        }

        // Kepala Kandang → mobile KK dashboard
        if ($user->role === 'kepala_kandang') {
            return redirect()->route('kk.dashboard');
        }

        // Super admin and admin → web dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
