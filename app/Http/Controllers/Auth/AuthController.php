<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Check for admin login
        if ($credentials['email'] === 'admin.passsna@gmail.com' &&
            $credentials['password'] === 'PaSSSna_log') {

            // Find or create admin user
            $admin = User::firstOrCreate(
                ['email' => 'admin.passsna@gmail.com'],
                [
                    'name' => 'Admin PaSSSna',
                    'password' => Hash::make('PaSSSna_log'),
                    'role' => 'admin',
                    'phone' => '+1 (555) 123-4567',
                    'is_active' => true
                ]
            );

            Auth::login($admin);
            return redirect()->route('admin.dashboard');
        }

        // Regular customer login
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // Check user role and redirect accordingly
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('customer.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:1000',
            // Match the Blade form field (`terms`), and accept older `agree_terms` too.
            'terms' => 'required_without:agree_terms|accepted',
            'agree_terms' => 'sometimes|accepted',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'address' => $validated['address'] ?? null,
            'role' => 'customer',
            'is_active' => true
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Registration successful! Welcome to PaSSSna Restaurant.');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Web route alias for password.email (routes/web.php expects sendResetLink).
     */
    public function sendResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', ['token' => $request->token]);
    }

    /**
     * Web route alias for password.update (routes/web.php expects resetPassword).
     */
    public function resetPassword(Request $request)
    {
        // If called via API, return JSON responses.
        if ($request->expectsJson()) {
            return $this->resetPasswordApi($request);
        }

        return $this->reset($request);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * API: send forgot-password link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 422);
    }

    /**
     * API: reset password with token.
     */
    public function resetPasswordApi(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed'
        ]);

        // Update basic info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address']
        ]);

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->new_password)
                ]);
            } else {
                return back()->with('error', 'Current password is incorrect.');
            }
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}
