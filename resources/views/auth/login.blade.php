@extends('layouts.auth')

@section('content')

<div class="rounded-2xl p-8" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px);">

    {{-- Logo --}}
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

    {{-- Success message (after logout or reset request) --}}
    @if(session('success'))
        <div class="mb-4 text-sm text-center rounded-lg px-4 py-2"
            style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #6ee7b7;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Login Form ── --}}
    <div id="panel-login">

        @if ($errors->any())
            <div class="mb-4 text-sm text-center rounded-lg px-4 py-2"
                style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
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

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded accent-green-600">
                    <span style="color: rgba(255,255,255,0.5);">Remember Me</span>
                </label>

                {{-- ADDED: forgot password link --}}
                <button type="button" onclick="showPanel('forgot')"
                    class="text-xs transition hover:underline"
                    style="color: rgba(255,255,255,0.4);">
                    Forgot Password?
                </button>
            </div>

            <button type="submit"
                class="w-full py-2.5 rounded-lg text-white font-medium transition hover:opacity-90"
                style="background: #16a34a;">
                Sign In
            </button>

        </form>

    </div>

    {{-- ── Forgot Password Form ── --}}
    {{-- ADDED: hidden by default, shown when user clicks Forgot Password --}}
    <div id="panel-forgot" class="hidden">

        @if(session('reset_success'))
            <div class="mb-4 text-sm text-center rounded-lg px-4 py-2"
                style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #6ee7b7;">
                {{ session('reset_success') }}
            </div>
        @endif

        <p class="text-sm text-center mb-6" style="color: rgba(255,255,255,0.5);">
            Enter your User ID and we will notify your administrator to reset your password.
        </p>

        <form method="POST" action="{{ route('password.request') }}" class="space-y-4">
            @csrf

            <div>
                <input type="text" name="ID"
                    placeholder="Enter your User ID"
                    required
                    class="w-full px-4 py-2.5 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-600"
                    style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
            </div>

            <button type="submit"
                class="w-full py-2.5 rounded-lg text-white font-medium transition hover:opacity-90"
                style="background: #16a34a;">
                Request Password Reset
            </button>

        </form>

        {{-- Back to login --}}
        <button type="button" onclick="showPanel('login')"
            class="w-full text-center text-xs mt-4 transition hover:underline"
            style="color: rgba(255,255,255,0.4);">
            ← Back to Sign In
        </button>

    </div>

</div>

<script>
    // ADDED: toggle between login and forgot password panels
    function showPanel(panel) {
        document.getElementById('panel-login').classList.add('hidden');
        document.getElementById('panel-forgot').classList.add('hidden');
        document.getElementById('panel-' + panel).classList.remove('hidden');
    }

    // ADDED: if reset_success session exists, show forgot panel automatically
    // so user sees the confirmation message
    @if(session('reset_success'))
        showPanel('forgot');
    @endif
</script>

@endsection