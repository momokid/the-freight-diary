@extends('layouts.auth')

@section('content')

<div class="rounded-2xl p-8" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px);">

    <!-- Logo -->
    <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20">
            <img src="/favicon.svg" alt="AnwarVerse Logo" class="w-full h-full">
        </div>
        <h1 class="mt-4 text-xl font-semibold text-white">
            The Freight Diary
        </h1>
        <p class="text-sm text-green-400 mt-1">
            Intelligent Logistics Platform
        </p>
        <p class="text-xs mt-1" style="color: rgba(255,255,255,0.3);">
            Built by AnwarVerse Ltd • v2.0 Prime Rebuild
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 text-sm text-center rounded-lg px-4 py-2" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #6ee7b7;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 text-sm text-center rounded-lg px-4 py-2" style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;">
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
                class="w-full px-4 py-2.5 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-600"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
        </div>

        <div>
            <input type="password" name="password"
                placeholder="Password"
                required
                class="w-full px-4 py-2.5 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-600"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
        </div>

        <div class="flex items-center text-sm">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded accent-green-600">
                <span style="color: rgba(255,255,255,0.5);">Remember Me</span>
            </label>
        </div>

        <button type="submit"
            class="w-full py-2.5 rounded-lg text-white font-medium transition hover:opacity-90"
            style="background: #16a34a;">
            Sign In
        </button>

    </form>

</div>

@endsection