@extends('layouts.guest')

@section('content')

<div class="bg-white shadow-xl rounded-2xl p-8">

    <!-- Logo -->
    <div class="flex flex-col items-center mb-6">
        <div class="w-16 h-16 bg-slate-900 rounded-xl flex items-center justify-center">
            <span class="text-white text-2xl font-bold"><i class="bi bi-truck"></i></span>
        </div>
        <h1 class="mt-4 text-xl font-semibold text-gray-800">
           The Freight Diary
        </h1>
        <p class="text-sm text-gray-500">
            Intelligent Logistics Platform
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Built by AnwarVerse Ltd • v2.0 Prime Rebuild
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600 text-sm text-center">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="/login" class="space-y-4">
        @csrf

        <div>
            <input type="text" name="ID"
                value="{{ old('ID') }}"
                placeholder="Username"
                required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-slate-900 focus:outline-none">
        </div>

        <div>
            <input type="password" name="password"
                placeholder="Password"
                required
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-slate-900 focus:outline-none">
        </div>

        <div class="flex items-center text-sm">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="remember" class="rounded">
                <span>Remember Me</span>
            </label>
        </div>

        <button type="submit"
            class="w-full bg-slate-900 text-white py-2 rounded-lg hover:bg-slate-800 transition">
            Sign In
        </button>
    </form>

</div>

@endsection