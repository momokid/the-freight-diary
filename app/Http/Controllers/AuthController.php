<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = [
            'ID' => $request->ID,
            'password' => $request->password,
            'Stats' => 1 // only active users
        ];


        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {

            DB::table('user_login_logs')->insert([
                'username' => $request->ID,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success'
            ]);

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        DB::table('user_login_logs')->insert([
            'username'   => $request->ID,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'failed'
        ]);

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
