@extends('layouts.auth')

@section('content')

<div class="rounded-2xl p-8" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px);">

    {{-- Logo --}}
    <div class="flex flex-col items-center mb-8">
        <div class="w-20 h-20">
            <img src="/favicon.svg" alt="AnwarVerse Logo" class="w-full h-full">
        </div>
        <h1 class="mt-4 text-xl font-semibold text-white">
            Change Your Password
        </h1>
        <p class="text-sm text-green-400 mt-1">
            You must set a new password to continue
        </p>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="mb-4 text-sm text-center rounded-lg px-4 py-2"
            style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #fca5a5;">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf

        {{-- New Password --}}
        <div>
            <label class="block text-xs font-medium mb-1.5"
                style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em;">
                NEW PASSWORD
            </label>
            <input
                type="password"
                name="password"
                placeholder="Enter new password"
                required
                minlength="6"
                class="w-full px-4 py-2.5 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-600"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-xs font-medium mb-1.5"
                style="color: rgba(255,255,255,0.5); letter-spacing: 0.05em;">
                CONFIRM PASSWORD
            </label>
            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm new password"
                required
                minlength="6"
                class="w-full px-4 py-2.5 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-600"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
        </div>

        {{-- Password requirements --}}
        <p class="text-xs" style="color: rgba(255,255,255,0.3);">
            Password must be at least 6 characters long
        </p>

        <button type="submit"
            class="w-full py-2.5 rounded-lg text-white font-medium transition hover:opacity-90"
            style="background: #16a34a;">
            Set New Password
        </button>

    </form>

</div>

@endsection