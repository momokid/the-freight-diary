<script>
    window.CommandCenterConfig = {
        resolveUrl: '{{ route('command-center.resolve') }}',
        runUrl: '{{ route('command-center.run') }}',
        csrf: '{{ csrf_token() }}',
        verbs: @json(\App\Services\AgentVerbService::forJs()),
    };
</script>

<div id="cc-overlay" aria-hidden="true">

    <div id="cc-backdrop" onclick="window.CommandCenter.close()"></div>

    <div id="cc-panel" role="dialog" aria-modal="true" aria-label="Command Center">

        {{-- ── Input row ── --}}
        <div id="cc-input-row">
            <svg id="cc-lead-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            <input type="text" id="cc-input" autocomplete="off" spellcheck="false"
                placeholder="Search a BL, container, consignee — or type an instruction">

            <span id="cc-mode-badge" data-mode="search">Search</span>

            <button type="button" id="cc-mic-btn" title="Speak" onclick="window.CommandCenter.toggleMic()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11a7 7 0 01-14 0m7 7v3m0-3a4 4 0 004-4V6a4 4 0 10-8 0v7a4 4 0 004 4z" />
                </svg>
                <span id="cc-mic-pulse"></span>
            </button>

            <button type="button" id="cc-close-btn" title="Close" onclick="window.CommandCenter.close()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- ── Mic status strip (hidden unless listening / erroring) ── --}}
        <div id="cc-mic-status" data-state="idle">
            <span id="cc-mic-status-text"></span>
        </div>

        {{-- ── Body: exactly one state visible at a time ── --}}
        <div id="cc-body">

            {{-- Empty state — recent searches from localStorage --}}
            <div id="cc-state-empty" class="cc-state">
                <p class="cc-section-label">Recent</p>
                <div id="cc-recents"></div>
                <p id="cc-no-recents" class="cc-hint-text">
                    Start typing to search, or describe what you want done.
                </p>
            </div>

            {{-- Results state — groups injected by layout.js --}}
            <div id="cc-state-results" class="cc-state" hidden></div>

            {{-- Thread state — Phase 4 agent conversation; stubbed for now --}}
            <div id="cc-state-thread" class="cc-state" hidden></div>

            {{-- Loading --}}
            <div id="cc-state-loading" class="cc-state" hidden>
                <p class="cc-hint-text">Searching…</p>
            </div>

            {{-- No matches --}}
            <div id="cc-state-none" class="cc-state" hidden>
                <p class="cc-hint-text">No matching records.</p>
            </div>

        </div>

        {{-- ── Footer hints ── --}}
        <div id="cc-footer">
            <span><kbd>↑</kbd><kbd>↓</kbd> navigate</span>
            <span><kbd>↵</kbd> <span id="cc-enter-hint">open</span></span>
            <span><kbd>esc</kbd> close</span>
        </div>

    </div>
</div>

<style>
    /* ── Overlay shell ── */
    #cc-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: none;
    }

    #cc-overlay.cc-open {
        display: block;
    }

    #cc-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
    }

    #cc-panel {
        --cc-scale: 1.3;
        position: relative;
        width: 1000px;
        max-width: calc(100vw - 32px);
        margin: 6vh auto 0;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.28);
        overflow: hidden;
        animation: cc-in 0.12s ease-out;
    }

    .cc-kbd {
        display: inline-block;
        padding: 1px 5px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--content-bg);
        font-family: inherit;
        font-size: 11px;
    }

    @keyframes cc-in {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Input row ── */
    #cc-input-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    #cc-lead-icon {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        color: var(--text-muted);
    }

    #cc-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        color: var(--text-primary);
        font-size: 15px;
    }

    #cc-input::placeholder {
        color: var(--text-muted);
    }

    #cc-mode-badge {
        flex-shrink: 0;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 3px 8px;
        border-radius: 999px;
        background: rgba(24, 95, 165, 0.12);
        color: #185FA5;
    }

    #cc-mode-badge[data-mode="agent"] {
        background: rgba(21, 128, 61, 0.12);
        color: #15803d;
    }

    #cc-mic-btn,
    #cc-close-btn {
        position: relative;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
    }

    #cc-mic-btn:hover,
    #cc-close-btn:hover {
        background: var(--content-bg);
        color: var(--text-primary);
    }

    #cc-mic-btn svg,
    #cc-close-btn svg {
        width: 17px;
        height: 17px;
    }

    #cc-mic-btn[data-listening="1"] {
        color: #b91c1c;
    }

    #cc-mic-pulse {
        display: none;
        position: absolute;
        inset: 0;
        border-radius: 8px;
        border: 2px solid #b91c1c;
        animation: cc-pulse 1.1s ease-out infinite;
    }

    #cc-mic-btn[data-listening="1"] #cc-mic-pulse {
        display: block;
    }

    @keyframes cc-pulse {
        0% {
            opacity: 0.9;
            transform: scale(1);
        }

        100% {
            opacity: 0;
            transform: scale(1.45);
        }
    }

    /* ── Mic status strip ── */
    #cc-mic-status {
        display: none;
        padding: 7px 16px;
        font-size: 12px;
        border-bottom: 1px solid var(--border-color);
    }

    #cc-mic-status[data-state="listening"],
    #cc-mic-status[data-state="processing"] {
        display: block;
        color: #185FA5;
        background: rgba(24, 95, 165, 0.06);
    }

    #cc-mic-status[data-state="error"] {
        display: block;
        color: #b91c1c;
        background: rgba(185, 28, 28, 0.06);
    }

    /* ── Body ── */
    #cc-body {
        max-height: 68vh;
        overflow-y: auto;
        padding: 8px 0;
    }

    .cc-section-label {
        padding: 6px 16px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
    }

    .cc-hint-text {
        padding: 14px 16px;
        font-size: 13px;
        color: var(--text-muted);
    }

    /* Result rows — rendered by layout.js, styled here */
    .cc-row {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 9px 16px;
        cursor: pointer;
    }

    .cc-row:hover,
    .cc-row.cc-active {
        background: var(--content-bg);
    }

    .cc-row.cc-active {
        box-shadow: inset 3px 0 0 #185FA5;
    }

    .cc-row-icon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: var(--text-muted);
    }

    .cc-row-main {
        flex: 1;
        min-width: 0;
    }

    .cc-row-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cc-row-title.cc-mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        letter-spacing: 0.02em;
    }

    .cc-row-meta {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ── Thread ── */
    .cc-thread {
        padding: 14px 16px;
    }

    .cc-thread-you {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .cc-thread-you::before {
        content: 'You: ';
        font-weight: 600;
    }

    /* ── Reply: three lines, three jobs ── */
    .cc-thread-head {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        color: var(--text-primary);
        margin-bottom: 3px;
    }

    .cc-thread-sub {
        font-size: 13px;
        line-height: 1.5;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    /* The sentence the user acts on — everything else is context */
    .cc-thread-action {
        font-size: 13.5px;
        font-weight: 500;
        line-height: 1.5;
        color: var(--text-primary);
        padding: 8px 12px;
        margin-bottom: 12px;
        border-left: 3px solid #185FA5;
        border-radius: 0 6px 6px 0;
        background: rgba(24, 95, 165, 0.05);
    }

    /* Amber warning, distinct from the red used for genuine errors */
    .cc-thread-flag.cc-flag-warn {
        background: rgba(146, 64, 14, 0.1);
        color: #92400e;
    }

    /* ── Fact values ── */
    .cc-facts dd.cc-mono {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        letter-spacing: 0.02em;
    }

    .cc-facts dd.cc-status {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 2px 8px;
        border-radius: 999px;
    }

    .cc-status-gray {
        background: #f3f4f6;
        color: #374151;
    }

    .cc-status-blue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .cc-status-amber {
        background: #fef3c7;
        color: #92400e;
    }

    .cc-status-purple {
        background: #f3e8ff;
        color: #7e22ce;
    }

    .cc-status-green {
        background: #dcfce7;
        color: #15803d;
    }

    .cc-status-teal {
        background: #ccfbf1;
        color: #0f766e;
    }

    .cc-status-red {
        background: #fee2e2;
        color: #b91c1c;
    }

    .cc-thread-working {
        font-size: 13px;
        color: var(--text-muted);
    }

    .cc-thread-reply {
        font-size: 14px;
        line-height: 1.55;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .cc-thread-error {
        font-size: 13px;
        color: #b91c1c;
    }

    .cc-thread-flag {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 2px 8px;
        border-radius: 999px;
        background: rgba(185, 28, 28, 0.1);
        color: #b91c1c;
        margin-bottom: 8px;
    }

    .cc-thread-fix {
        display: inline-block;
        font-size: calc(13px * var(--cc-scale));
        font-weight: 600;
        color: #185FA5;
        text-decoration: none;
        margin-bottom: 10px;
    }

    .cc-thread-fix:hover {
        text-decoration: underline;
    }

    .cc-facts {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 4px 14px;
        font-size: 12px;
        padding-top: 10px;
        border-top: 1px solid var(--border-color);
    }

    .cc-facts dt {
        color: var(--text-muted);
        white-space: nowrap;
    }

    .cc-facts dd {
        color: var(--text-primary);
        margin: 0;
    }

    /* ── Footer ── */
    #cc-footer {
        display: flex;
        gap: 16px;
        padding: 9px 16px;
        border-top: 1px solid var(--border-color);
        background: var(--content-bg);
        font-size: 11px;
        color: var(--text-muted);
    }

    #cc-footer kbd {
        display: inline-block;
        min-width: 16px;
        padding: 1px 4px;
        margin-right: 3px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--card-bg);
        font-family: inherit;
        font-size: 10px;
        text-align: center;
    }

    /* ── Mobile: full screen ── */
    @media (max-width: 640px) {
        #cc-panel {
            width: 100%;
            max-width: 100%;
            height: 100dvh;
            margin: 0;
            border: none;
            border-radius: 0;
        }

        #cc-body {
            max-height: calc(100dvh - 118px);
        }

        #cc-footer {
            display: none;
        }
    }
</style>
