<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Log login attempts with status (success, failed, inactive)
    private function logLoginAttempt(Request $request, string $status): void
    {
        DB::table('user_login_logs')->insert([
            'username'   => $request->ID,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => $status,
        ]);
    }

    // Show login form
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    // Forgot password handler
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'ID' => ['required', 'string'],
        ]);

        // Silently set reset_requested = 1 if user exists
        // We don't tell the requester whether the ID was found or not
        $user = User::where('ID', $request->ID)->first();

        if ($user) {
            $user->reset_requested = 1;
            $user->save();
        }

        return redirect()->route('login')
            ->with('reset_success', 'Your request has been submitted. Please contact your administrator.');
    }

    // ADDED: shows the change password form
    public function showChangePassword()
    {
        // If user doesn't need to change password, redirect to dashboard
        if (! Auth::user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.change-password');
    }

    // ADDED: handles the change password form submission
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)        // minimum 8 characters
                    ->mixedCase()       // at least one uppercase and one lowercase
                    ->numbers(),        // at least one number
            ],
            'password_confirmation' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Update password and clear the must_change_password flag
        $user->HashPassword = bcrypt($request->password);
        $user->must_change_password = 0;
        $user = User::where('ID', Auth::user()->ID)->firstOrFail();

        // Update password and clear the must_change_password flag
        $user->HashPassword         = bcrypt($request->password);
        $user->must_change_password = 0;
        $user->save();

        // Invalidates old session token 
        $request->session()->regenerate();


        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully. Welcome back!');
    }

    //Login function with account status check and logging
    public function login(Request $request)
    {
        $request->validate([
            'ID'       => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'ID' => $request->ID,
            'password' => $request->password,
        ];


        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            if (Auth::user()->Stats !== 1) {

                $this->logLoginAttempt($request, 'inactive');

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'ID' => 'Your account is inactive. Please contact support.',
                ]);
            }

            $this->logLoginAttempt($request, 'success');

            $request->session()->regenerate();

            // if user must change password, redirect to change password screen
            if (Auth::user()->must_change_password) {
                return redirect()->route('password.change');
            }
            return redirect()->intended('/dashboard');
        }

        $this->logLoginAttempt($request, 'failed');

        return redirect()->route('login')->withErrors([
            'ID' => 'Invalid credentials.',
        ]);
    }

    // Logout function
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
