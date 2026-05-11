<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // First, check if user exists with this email
        $user = \App\Models\User::where('email', $request->email)->first();

        // If user doesn't exist, show generic error
        if (!$user) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        // If user is a student and not verified, show specific message
        if ($user->isStudent() && !$user->is_verified) {
            return back()->withErrors([
                'email' => 'Your account is pending verification. Please wait for the registrar to verify your account before logging in.',
            ])->onlyInput('email');
        }

        // Check if registrar account is active
        if ($user->isRegistrar() && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact the system administrator.',
            ])->onlyInput('email');
        }

        // Attempt to authenticate
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}