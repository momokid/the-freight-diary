@extends('layouts.app')

@section('title', 'OCR Cache Monitor')
@section('page-title', 'OCR Cache Monitor')

@section('content')

    {{-- Summary strip --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.25rem;">
        <div class="card">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Cached Documents</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                {{ number_format($summary['totalEntries']) }}</p>
        </div>
        <div class="card">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Total Lookups</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">
                {{ number_format($summary['totalHits']) }}</p>
        </div>
        <div class="card">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">API Calls Saved</p>
            <p style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">{{ number_format($summary['hitsSaved']) }}</p>
        </div>
    </div>

    <div class="card" style="padding: 0;">
        <div
            style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color);">
            <p class="form-title" style="margin: 0;">Cached Extractions</p>
            <button onclick="clearAllCache()" id="clear-cache-btn"
                style="padding: 8px 16px; border-radius: 8px; border: 1px solid #ef4444; background: transparent; color: #ef4444; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                Clear All Cache
            </button>
        </div>

        @if ($entries->isEmpty())
            <div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                No cached OCR extractions yet.
            </div>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th>File Hash</th>
                        <th>Provider</th>
                        <th style="text-align: center;">Hits</th>
                        <th>Cached On</th>
                        <th>Expires</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td class="td-mono" style="font-size: 0.75rem;">{{ substr($entry->FileHash, 0, 16) }}…</td>
                            <td class="td-muted">{{ ucfirst($entry->Provider) }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $entry->HitCount }}</td>
                            <td class="td-muted">{{ \Carbon\Carbon::parse($entry->CreatedAt)->format('d M Y, h:i A') }}</td>
                            <td class="td-muted">{{ \Carbon\Carbon::parse($entry->ExpiresAt)->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding: 1rem 1.25rem;">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function clearAllCache() {
            if (!confirm('Clear all cached OCR extractions? This cannot be undone.')) return;

            const btn = document.getElementById('clear-cache-btn');
            btn.disabled = true;
            btn.textContent = 'Clearing...';

            fetch('{{ route('settings.ocr-cache.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to clear cache.');
                        btn.disabled = false;
                        btn.textContent = 'Clear All Cache';
                    }
                })
                .catch(() => {
                    alert('Something went wrong.');
                    btn.disabled = false;
                    btn.textContent = 'Clear All Cache';
                });
        }
    </script>
@endpush
