<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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
