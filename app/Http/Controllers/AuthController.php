<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //
    private function logLoginAttempt(Request $request, string $status): void
    {
        DB::table('user_login_logs')->insert([
            'username'   => $request->ID,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => $status,
        ]);
    }


    public function showLogin()
    {
        return view('auth.login');
    }

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
                Auth::logout();

                $this->logLoginAttempt($request, 'inactive');

                return back()->withErrors([
                    'ID' => 'Your account is inactive. Please contact support.',
                ]);
            }

            $this->logLoginAttempt($request, 'success');

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        $this->logLoginAttempt($request, 'failed');

        return back()->withErrors([
            'ID' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
