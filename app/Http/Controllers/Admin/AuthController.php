<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the administrative login view.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->hasRole(['super-admin', 'admin', 'editor', 'author'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        // Login rate limiting: 5 attempts per 1 minute
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our administrative records.',
            ]);
        }

        // Check if user has administrative privileges
        if (!$user->hasRole(['super-admin', 'admin', 'editor', 'author'])) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Access denied. Account is not authorized for administration portal access.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->boolean('remember'));

        // Prevent session fixation
        $request->session()->regenerate();

        // Audit log
        ActivityLogger::log('login', $user, ['ip' => $request->ip()]);

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Log the user out of the administration portal.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            ActivityLogger::log('logout', $user, ['ip' => $request->ip()]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been successfully logged out.');
    }

    /**
     * Display password reset request form.
     */
    public function showForgotPasswordForm(): View
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send a password reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $throttleKey = 'password-reset|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many reset attempts. Please wait {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($throttleKey, 120);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->hasRole(['super-admin', 'admin', 'editor', 'author'])) {
            // Note: Since SMTP is not configured in Phase 2, we log this attempt and inform the administrator
            ActivityLogger::log('password_reset_request', $user, ['email' => $request->email]);

            return back()->with('status', 'If an administrative account exists with that email, password reset instructions have been recorded.');
        }

        return back()->with('status', 'If an administrative account exists with that email, password reset instructions have been recorded.');
    }
}
