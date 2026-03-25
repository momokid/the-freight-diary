<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        //count users fwith pending pasword request change
        $pendingResetCount = User::where('reset_requested', 1)->count();

        return view('dashboard', compact('pendingResetCount'));
    }
}
