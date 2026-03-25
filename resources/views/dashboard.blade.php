@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    {{-- ADDED: pending password reset notification --}}
   {{-- CHANGED: combined into single @hasAccess check to avoid nested directive parse error --}}
    {{-- CHANGED: combined into single @hasAccess check to avoid nested directive parse error --}}
    {{-- CHANGED: using plain @if instead of @hasAccess to avoid Blade parse errors --}}
@if(isset($userAuth) && $userAuth->hasPermission('UserPrivilege') && $pendingResetCount > 0)
    <div class="rounded-xl px-4 py-3 mb-6 flex items-center justify-between"
        style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2);padding: 0.5rem 0.75rem;">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="#ef4444" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-medium" style="color: #ef4444;">
                {{ $pendingResetCount }} {{ Str::plural('user', $pendingResetCount) }}
                {{ $pendingResetCount === 1 ? 'has' : 'have' }} requested a password reset
            </p>
        </div>
        <a href="{{ route('settings.user-privilege.index') }}"
            class="text-xs font-medium px-3 py-1.5 rounded-lg transition hover:opacity-90"
            style="background: #ef4444; color: white; padding: 0.375rem 0.75rem;">
            View Requests
        </a>
    </div>
@endif

<p style="color: var(--text-primary);">Dashboard content coming soon.</p>

@endsection
