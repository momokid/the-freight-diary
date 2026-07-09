@extends('layouts.app')

@section('title', 'Error Log Tickets')
@section('page-title', 'Error Log Tickets')

@section('content')

    {{-- Summary strip — also acts as filter tabs --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.25rem;">
        <div class="card tab-card" data-status="new" onclick="switchTab('new')" style="cursor: pointer; border-color: #ef4444;">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">New</p>
            <p id="count-new" style="font-size: 1.5rem; font-weight: 700; color: #ef4444;">—</p>
        </div>
        <div class="card tab-card" data-status="acknowledged" onclick="switchTab('acknowledged')" style="cursor: pointer;">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Acknowledged</p>
            <p id="count-acknowledged" style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">—</p>
        </div>
        <div class="card tab-card" data-status="resolved" onclick="switchTab('resolved')" style="cursor: pointer;">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Resolved</p>
            <p id="count-resolved" style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">—</p>
        </div>
        <div class="card tab-card" data-status="all" onclick="switchTab('all')" style="cursor: pointer;">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">All Tickets</p>
            <p id="count-all" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">—</p>
        </div>
    </div>

    <div class="card" style="padding: 0;">
        <div id="table-wrapper">
            <div style="padding: 2rem; text-align: center; color: var(--text-muted);">Loading tickets...</div>
        </div>
        <div id="pagination-wrapper" style="padding: 1rem 1.25rem; display: flex; justify-content: center; gap: 6px;"></div>
    </div>

    {{-- Slide-over panel --}}
    <div id="slideover-backdrop" onclick="closeSlideover()"
        style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 60;"></div>

    <div id="slideover-panel"
        style="display: none; position: fixed; top: 0; right: 0; bottom: 0; width: 100%; max-width: 520px; background: var(--card-bg); z-index: 61; box-shadow: -4px 0 20px rgba(0,0,0,0.15); overflow-y: auto;">

        <div
            style="padding: 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; background: var(--card-bg); z-index: 1;">
            <p class="form-title" style="margin: 0;">Ticket Details</p>
            <button onclick="closeSlideover()"
                style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 1.3rem;">✕</button>
        </div>

        <div style="padding: 1.25rem;">
            <div id="so-status-badge"
                style="display: inline-block; padding: 4px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; margin-bottom: 1rem;">
            </div>

            <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">EXCEPTION</p>
            <p id="so-exception"
                style="font-size: 0.9rem; font-weight: 600; color: var(--text-primary); margin-bottom: 14px;"></p>

            <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">MESSAGE</p>
            <p id="so-message" style="font-size: 0.85rem; color: var(--text-primary); margin-bottom: 14px;"></p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">ROUTE</p>
                    <p id="so-route" style="font-size: 0.8rem; color: var(--text-primary);"></p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">USER</p>
                    <p id="so-username" style="font-size: 0.8rem; color: var(--text-primary);"></p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">FILE : LINE</p>
                    <p id="so-file" style="font-size: 0.75rem; color: var(--text-primary); word-break: break-all;"></p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">OCCURRENCES</p>
                    <p id="so-occurrences" style="font-size: 0.8rem; color: var(--text-primary);"></p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">FIRST SEEN</p>
                    <p id="so-first-seen" style="font-size: 0.8rem; color: var(--text-primary);"></p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px;">LAST SEEN</p>
                    <p id="so-last-seen" style="font-size: 0.8rem; color: var(--text-primary);"></p>
                </div>
            </div>

            <details style="margin-bottom: 1.25rem;">
                <summary style="cursor: pointer; font-size: 0.8rem; font-weight: 600; color: var(--text-primary);">Show
                    Stack Trace</summary>
                <pre id="so-trace"
                    style="font-size: 0.7rem; background: var(--content-bg); padding: 10px; border-radius: 6px; overflow-x: auto; margin-top: 8px; white-space: pre-wrap;"></pre>
            </details>

            <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <button onclick="setStatus('acknowledged')" id="btn-acknowledge"
                    style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #f59e0b; background: transparent; color: #f59e0b; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                    Acknowledge
                </button>
                <button onclick="setStatus('resolved')" id="btn-resolve"
                    style="flex: 1; padding: 10px; border-radius: 8px; border: none; background: #16a34a; color: white; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                    Mark Resolved
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const CSRF = '{{ csrf_token() }}';
        let currentTab = 'new';
        let currentPage = 1;
        let currentEntry = null;

        function loadTickets(status = currentTab, page = 1) {
            currentTab = status;
            currentPage = page;

            document.querySelectorAll('.tab-card').forEach(el => {
                el.style.outline = el.dataset.status === status ? '2px solid #16a34a' : 'none';
            });

            fetch(`{{ route('settings.error-log.data') }}?status=${status}&page=${page}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('count-new').textContent = data.summary.new;
                    document.getElementById('count-acknowledged').textContent = data.summary.acknowledged;
                    document.getElementById('count-resolved').textContent = data.summary.resolved;
                    document.getElementById('count-all').textContent = data.summary.new + data.summary.acknowledged +
                        data.summary.resolved;

                    renderTable(data.entries);
                    renderPagination(data.pagination);
                });
        }

        function switchTab(status) {
            loadTickets(status, 1);
        }

        function renderTable(entries) {
            const wrapper = document.getElementById('table-wrapper');

            if (!entries.length) {
                wrapper.innerHTML =
                    `<div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">No tickets in this view.</div>`;
                return;
            }

            const statusColors = {
                new: '#ef4444',
                acknowledged: '#f59e0b',
                resolved: '#16a34a'
            };

            wrapper.innerHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Exception</th>
                    <th>Route</th>
                    <th style="text-align: center;">Occurrences</th>
                    <th>Last Seen</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ${entries.map((e, i) => `
                        <tr onclick="openSlideover(${i})" style="cursor: pointer;">
                            <td style="font-size: 0.8rem; font-weight: 500; color: var(--text-primary);">${e.ExceptionClass.split('\\\\').pop()}</td>
                            <td class="td-muted">${e.Route ?? '—'}</td>
                            <td style="text-align: center; font-weight: 600;">${e.OccurrenceCount}</td>
                            <td class="td-muted">${new Date(e.LastSeenAt).toLocaleString()}</td>
                            <td>
                                <span style="display: inline-block; padding: 2px 10px; border-radius: 5px; font-size: 0.7rem; font-weight: 700; color: white; background: ${statusColors[e.Status]};">
                                    ${e.Status.toUpperCase()}
                                </span>
                            </td>
                        </tr>
                    `).join('')}
            </tbody>
        </table>`;

            window._currentEntries = entries;
        }

        function renderPagination(pagination) {
            const wrapper = document.getElementById('pagination-wrapper');
            if (pagination.lastPage <= 1) {
                wrapper.innerHTML = '';
                return;
            }

            let html = '';
            for (let p = 1; p <= pagination.lastPage; p++) {
                html += `<button onclick="loadTickets(currentTab, ${p})"
                    style="padding: 6px 12px; border-radius: 6px; border: 1px solid var(--border-color); background: ${p === pagination.currentPage ? '#16a34a' : 'transparent'}; color: ${p === pagination.currentPage ? 'white' : 'var(--text-primary)'}; font-size: 0.8rem; cursor: pointer;">
                    ${p}
                </button>`;
            }
            wrapper.innerHTML = html;
        }

        function openSlideover(index) {
            const entry = window._currentEntries[index];
            currentEntry = entry;

            const statusColors = {
                new: '#ef4444',
                acknowledged: '#f59e0b',
                resolved: '#16a34a'
            };

            document.getElementById('so-status-badge').textContent = entry.Status.toUpperCase();
            document.getElementById('so-status-badge').style.background = statusColors[entry.Status];
            document.getElementById('so-status-badge').style.color = 'white';

            document.getElementById('so-exception').textContent = entry.ExceptionClass;
            document.getElementById('so-message').textContent = entry.Message;
            document.getElementById('so-route').textContent = entry.Route ?? '—';
            document.getElementById('so-username').textContent = entry.Username ?? 'System / Console';
            document.getElementById('so-file').textContent = entry.File + ' : ' + entry.Line;
            document.getElementById('so-occurrences').textContent = entry.OccurrenceCount;
            document.getElementById('so-first-seen').textContent = new Date(entry.FirstSeenAt).toLocaleString();
            document.getElementById('so-last-seen').textContent = new Date(entry.LastSeenAt).toLocaleString();
            document.getElementById('so-trace').textContent = entry.Trace;

            document.getElementById('btn-acknowledge').style.display = entry.Status === 'new' ? 'block' : 'none';
            document.getElementById('btn-resolve').style.display = entry.Status !== 'resolved' ? 'block' : 'none';

            document.getElementById('slideover-backdrop').style.display = 'block';
            document.getElementById('slideover-panel').style.display = 'block';
        }

        function closeSlideover() {
            document.getElementById('slideover-backdrop').style.display = 'none';
            document.getElementById('slideover-panel').style.display = 'none';
        }

        function setStatus(status) {
            if (!currentEntry) return;

            fetch(`/settings/error-log/${currentEntry.ID}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        status
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        closeSlideover();
                        loadTickets(currentTab, currentPage);
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', () => loadTickets('new', 1));
    </script>
@endpush
