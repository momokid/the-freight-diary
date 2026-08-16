/**
 * Freight Diary v2.0 — Layout JavaScript
 * Handles: theme toggle, sidebar toggle, submenus, user dropdown, active submenu detection
 * Location: public/js/layout.js
 */

// reveal page after CSS loads — prevents FOUC
document.addEventListener("DOMContentLoaded", function () {
    // ── Scroll sidebar to active link on every page load ──
    const activeLink = document.querySelector(
        "#sidebar nav .submenu-link.active",
    );
    if (activeLink) {
        activeLink.scrollIntoView({ block: "nearest", behavior: "instant" });
    }
});

document.body.style.visibility = "visible";

const sidebar = document.getElementById("sidebar");
const mainWrapper = document.getElementById("main-wrapper");
const overlay = document.getElementById("sidebar-overlay");
const html = document.documentElement;

// ── Theme ──
const savedTheme = localStorage.getItem("fd-theme") || "dark";
applyTheme(savedTheme);

function applyTheme(theme) {
    html.setAttribute("data-theme", theme);
    localStorage.setItem("fd-theme", theme);
    document.getElementById("icon-dark").style.display =
        theme === "light" ? "none" : "";
    document.getElementById("icon-light").style.display =
        theme === "dark" ? "none" : "";
}

function toggleTheme() {
    const current = html.getAttribute("data-theme");
    applyTheme(current === "dark" ? "light" : "dark");
}

// ── Sidebar toggle ──
const isDesktop = () => window.innerWidth >= 1024;
const savedCollapsed = localStorage.getItem("fd-sidebar") === "collapsed";
if (isDesktop() && savedCollapsed) {
    sidebar.classList.add("collapsed");
    mainWrapper.classList.add("sidebar-collapsed");
}

function toggleSidebar() {
    if (isDesktop()) {
        const collapsed = sidebar.classList.toggle("collapsed");
        mainWrapper.classList.toggle("sidebar-collapsed", collapsed);
        localStorage.setItem(
            "fd-sidebar",
            collapsed ? "collapsed" : "expanded",
        );
    } else {
        const open = sidebar.classList.toggle("mobile-open");
        overlay.classList.toggle("active", open);
    }
}

function closeSidebar() {
    sidebar.classList.remove("mobile-open");
    overlay.classList.remove("active");
}

// Opening a submenu closes all others — only one open at a time.
function toggleSubmenu(key) {
    // Save scroll position before any DOM changes
    const nav = document.querySelector("#sidebar nav");
    const scrollTop = nav ? nav.scrollTop : 0;

    const submenu = document.getElementById("submenu-" + key);
    const arrow = document.getElementById("arrow-" + key);
    if (!submenu) return;

    const isOpen = submenu.classList.contains("open");

    // Close all other open submenus first
    document.querySelectorAll(".submenu.open").forEach(function (el) {
        if (el.id !== "submenu-" + key) {
            el.classList.remove("open");
            el.classList.add("closed");
            const otherKey = el.id.replace("submenu-", "");
            const otherArrow = document.getElementById("arrow-" + otherKey);
            if (otherArrow) otherArrow.style.transform = "";
        }
    });

    // Toggle the clicked submenu
    submenu.classList.toggle("open", !isOpen);
    submenu.classList.toggle("closed", isOpen);
    if (arrow) arrow.style.transform = isOpen ? "" : "rotate(180deg)";

    // Restore scroll after browser reflow settles
    if (nav)
        requestAnimationFrame(() => {
            nav.scrollTop = scrollTop;
        });
}

// ── Auto-open active submenu on page load ──
function autoOpenActiveSubmenu() {
    const path = window.location.pathname;

    const submenus = {
        setup: [
            "/settings/ledger-control",
            "/settings/ledger-category",
            "/settings/ledger-account",
            "/settings/handling-charge",
            "/settings/disbursement-account",
            "/settings/user-privilege",
            "/settings/active-accounts",
        ],
        masterdata: [
            "/master-data/consignees",
            "/master-data/shippers",
            "/master-data/carriers",
            "/master-data/ports",
            "/master-data/commodities",
        ],
        consignment: ["/consignments", "/manifest", "/cmdts"],
        invoice: [
            "/invoice/house-bl",
            "/invoice/waybill",
            "/invoice/other-invoice",
            "/invoice/non-manifest",
        ],
        payment: [
            "/payment/declaration",
            "/payment/handl-charge",
            "/payment/serv-charge",
            "/payment/handling-charge-expense",
        ],
        accounting: ["/accounting/transaction"],
        disbursement: ["/disbursement"],
        edit: ["/edit-data"],
        reports: [
            "/reports",
            "/reports/operations",
            "/reports/client",
            "/reports/disbursement",
            "/reports/accounting",
            "/reports/management",
        ],
    };

    Object.entries(submenus).forEach(([key, paths]) => {
        const belongs = paths.some((p) => path.startsWith(p));
        if (belongs) {
            const submenu = document.getElementById("submenu-" + key);
            const arrow = document.getElementById("arrow-" + key);
            if (submenu) {
                submenu.classList.remove("closed");
                submenu.classList.add("open");
            }
            if (arrow) {
                arrow.style.transform = "rotate(180deg)";
            }
        }
    });
}

autoOpenActiveSubmenu();

// ── User dropdown ──
let userMenuJustOpened = false;

function toggleUserMenu() {
    const menu = document.getElementById("user-menu");
    const chevron = document.getElementById("user-menu-chevron");
    // CHANGED: using display style instead of Tailwind hidden class
    const isHidden = menu.style.display === "none" || menu.style.display === "";

    menu.style.display = isHidden ? "block" : "none";
    if (chevron) chevron.style.transform = isHidden ? "rotate(180deg)" : "";

    if (isHidden) {
        userMenuJustOpened = true;
    }
}

document.addEventListener("click", function (e) {
    if (userMenuJustOpened) {
        userMenuJustOpened = false;
        return;
    }
    const menu = document.getElementById("user-menu");
    const wrapper = document.getElementById("user-dropdown-wrapper");
    if (wrapper && !wrapper.contains(e.target)) {
        menu.style.display = "none";
        const chevron = document.getElementById("user-menu-chevron");
        if (chevron) chevron.style.transform = "";
    }
});

// ── Close sidebar on resize to desktop ──
window.addEventListener("resize", () => {
    if (isDesktop()) closeSidebar();
});

window.toggleTheme = toggleTheme;
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.toggleSubmenu = toggleSubmenu;
window.toggleUserMenu = toggleUserMenu;

// attach NProgress listeners after DOM is fully ready
window.addEventListener("load", function () {
    if (typeof NProgress !== "undefined") {
        NProgress.configure({ showSpinner: false });
        document.querySelectorAll("a[href]").forEach((link) => {
            if (
                link.href &&
                link.href.startsWith(window.location.origin) &&
                !link.href.includes("#") &&
                !link.target
            ) {
                link.addEventListener("click", () => NProgress.start());
            }
        });
        NProgress.done();
    }
});

// ── Refresh receipt number after a successful save ──
window.refreshReceipt = function (receiptNoId, receiptIdId, dateFieldId) {
    const dateEl = dateFieldId ? document.getElementById(dateFieldId) : null;
    const date =
        dateEl && dateEl.value
            ? dateEl.value
            : new Date().toISOString().split("T")[0];

    fetch(`/receipt/generate?date=${date}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
    })
        .then((res) => res.json())
        .then((data) => {
            const noEl = document.getElementById(receiptNoId);
            const idEl = document.getElementById(receiptIdId);
            if (noEl) noEl.value = data.receipt_no;
            if (idEl) idEl.value = data.id;
        });
};

// ── SearchDropdown — reusable typeahead component ──
window.SearchDropdown = class {
    constructor(options) {
        this.inputEl = document.getElementById(options.inputId);
        this.dropdownEl = document.getElementById(options.dropdownId);
        this.hiddenEl = options.hiddenId
            ? document.getElementById(options.hiddenId)
            : null;
        this.url = options.url;
        this.labelKey = options.labelKey;
        this.subKey = options.subKey ?? null;
        this.valueKey = options.valueKey;
        this.minLength = options.minLength ?? 2;
        this.delay = options.delay ?? 400;
        this.onSelect = options.onSelect ?? null;
        this.timer = null;

        if (!this.inputEl || !this.dropdownEl) return;
        this._bindEvents();
    }

    _bindEvents() {
        this.inputEl.addEventListener("input", () => {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this._search(), this.delay);
        });
        this.inputEl.addEventListener("keydown", (e) => {
            if (e.key === "Escape") this._close();
        });
        document.addEventListener("click", (e) => {
            if (
                !this.inputEl.contains(e.target) &&
                !this.dropdownEl.contains(e.target)
            ) {
                this._close();
            }
        });
    }

    _search() {
        const q = this.inputEl.value.trim();
        if (q.length < this.minLength) {
            this._close();
            return;
        }
        const sep = this.url.includes("?") ? "&" : "?";

        // Show loading skeleton
        this._showLoading();

        fetch(`${this.url}${sep}q=${encodeURIComponent(q)}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then((res) => (res.status === 429 ? [] : res.json()))
            .then((data) => {
                if (!data?.length) {
                    this._close();
                    return;
                }
                this._render(data);
            })
            .catch(() => this._close());
    }

    _showLoading() {
        // Clear existing content
        while (this.dropdownEl.firstChild)
            this.dropdownEl.removeChild(this.dropdownEl.firstChild);

        // Show 3 skeleton rows
        for (let i = 0; i < 3; i++) {
            const row = document.createElement("div");
            row.style.cssText =
                "padding: 10px 14px; border-bottom: 1px solid var(--border-color);";

            const line1 = document.createElement("div");
            line1.className = "skeleton";
            line1.style.cssText =
                "height: 12px; width: 70%; border-radius: 4px; margin-bottom: 4px;";

            const line2 = document.createElement("div");
            line2.className = "skeleton";
            line2.style.cssText =
                "height: 10px; width: 40%; border-radius: 4px;";

            row.appendChild(line1);
            row.appendChild(line2);
            this.dropdownEl.appendChild(row);
        }

        this.dropdownEl.style.display = "block";
    }

    _render(items) {
        while (this.dropdownEl.firstChild)
            this.dropdownEl.removeChild(this.dropdownEl.firstChild);
        items.forEach((item) => {
            const label = String(item[this.labelKey] ?? "");
            const sub = this.subKey ? String(item[this.subKey] ?? "") : "";
            const value = item[this.valueKey] ?? "";
            const row = document.createElement("div");
            row.style.cssText =
                "padding: 10px 14px; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid var(--border-color);";
            const labelEl = document.createElement("div");
            labelEl.style.cssText =
                "font-weight: 500; color: var(--text-primary);";
            labelEl.textContent = label;
            row.appendChild(labelEl);
            if (sub) {
                const subEl = document.createElement("div");
                subEl.style.cssText =
                    "color: var(--text-muted); font-size: 0.75rem;";
                subEl.textContent = sub;
                row.appendChild(subEl);
            }
            row.addEventListener("click", () => this.select(value, label));
            row.addEventListener(
                "mouseover",
                () => (row.style.background = "var(--content-bg)"),
            );
            row.addEventListener("mouseout", () => (row.style.background = ""));
            this.dropdownEl.appendChild(row);
        });
        this.dropdownEl.style.display = "block";
    }

    select(value, label) {
        this.inputEl.value = label;
        if (this.hiddenEl) this.hiddenEl.value = value;
        this._close();
        if (typeof this.onSelect === "function") this.onSelect(value, label);
    }

    _close() {
        this.dropdownEl.style.display = "none";
    }

    clear() {
        this.inputEl.value = "";
        if (this.hiddenEl) this.hiddenEl.value = "";
        this._close();
    }

    setValue(value, label) {
        this.inputEl.value = label;
        if (this.hiddenEl) this.hiddenEl.value = value;
    }
};

// ── Collapsed sidebar flyout submenus ─────────────────────────────────────
function initCollapsedFlyouts() {
    const sidebar = document.getElementById("sidebar");
    if (!sidebar) return;

    document.querySelectorAll(".nav-item-wrapper").forEach((wrapper) => {
        const submenu = wrapper.querySelector(".submenu");
        if (!submenu) return;

        let hideTimer = null;

        wrapper.addEventListener("mouseenter", () => {
            if (!sidebar.classList.contains("collapsed")) return;
            clearTimeout(hideTimer);

            const rect = wrapper.getBoundingClientRect();
            submenu.style.cssText = `
                display: block !important;
                position: fixed;
                left: 64px;
                top: ${rect.top}px;
                min-width: 200px;
                background: var(--tooltip-bg);
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 8px;
                padding: 6px 0;
                box-shadow: 4px 4px 16px rgba(0,0,0,0.3);
                z-index: 9999;
            `;
        });

        wrapper.addEventListener("mouseleave", () => {
            if (!sidebar.classList.contains("collapsed")) return;
            hideTimer = setTimeout(() => {
                submenu.style.cssText = "";
            }, 100);
        });

        submenu.addEventListener("mouseenter", () => {
            clearTimeout(hideTimer);
        });

        submenu.addEventListener("mouseleave", () => {
            hideTimer = setTimeout(() => {
                submenu.style.cssText = "";
            }, 100);
        });
    });
}

document.addEventListener("DOMContentLoaded", initCollapsedFlyouts);

window.CommandCenter = {
    // ── Config ──
    MIN_CHARS: 2,
    DEBOUNCE_MS: 300,
    RECENTS_KEY: "cc_recents",
    RECENTS_MAX: 5,
    SPEECH_LANG: "en-GB",

    // ── State ──
    open: false,
    ready: false,
    mode: "search", // 'search' | 'agent'
    inputModality: "text", // 'text' | 'speech'  → passed to agent_runs later
    rows: [], // flat list of currently rendered results
    activeIndex: -1,
    debounceTimer: null,
    abortController: null,
    lastFocused: null,
    recognition: null,
    listening: false,

    // ── Element cache ──
    el: {},

    init() {
        this.el = {
            overlay: document.getElementById("cc-overlay"),
            panel: document.getElementById("cc-panel"),
            input: document.getElementById("cc-input"),
            badge: document.getElementById("cc-mode-badge"),
            micBtn: document.getElementById("cc-mic-btn"),
            micStatus: document.getElementById("cc-mic-status"),
            micText: document.getElementById("cc-mic-status-text"),
            body: document.getElementById("cc-body"),
            stEmpty: document.getElementById("cc-state-empty"),
            stResults: document.getElementById("cc-state-results"),
            stThread: document.getElementById("cc-state-thread"),
            stNone: document.getElementById("cc-state-none"),
            stLoading: document.getElementById("cc-state-loading"),
            recents: document.getElementById("cc-recents"),
            noRecents: document.getElementById("cc-no-recents"),
            starters: document.getElementById("cc-starters"),
        };

        if (!this.el.overlay) return; // partial not included on this page

        this.ready = true;

        this.el.input.addEventListener("input", () => this.onType());
        this.el.input.addEventListener("keydown", (e) => this.onInputKey(e));

        this.setupSpeech();
    },

    // ── Open / close ──────────────────────────────────────────

    show() {
        if (!this.ready) return;

        if (this.open) {
            this.el.input.focus();
            return;
        }
        this.lastFocused = document.activeElement;
        this.open = true;
        this.el.overlay.classList.add("cc-open");
        this.el.overlay.setAttribute("aria-hidden", "false");
        this.renderRecents();

        this.renderStarters(this.starterList());

        this.setState("empty");
        this.el.input.focus();
    },

    close() {
        if (!this.ready || !this.open) return;
        if (this.listening) this.stopMic();

        this.open = false;
        this.el.overlay.classList.remove("cc-open");
        this.el.overlay.setAttribute("aria-hidden", "true");
        this.el.input.value = "";
        this.rows = [];
        this.activeIndex = -1;
        this.inputModality = "text";
        this.setMode("search");

        // Restore focus to whatever the user was doing before
        if (this.lastFocused && document.body.contains(this.lastFocused)) {
            this.lastFocused.focus();
        }
        this.lastFocused = null;
    },

    // ── State switching ───────────────────────────────────────

    setState(name) {
        this.el.stEmpty.hidden = name !== "empty";
        this.el.stResults.hidden = name !== "results";
        this.el.stThread.hidden = name !== "thread";
        this.el.stNone.hidden = name !== "none";
        this.el.stLoading.hidden = name !== "loading";
    },

    setMode(mode) {
        this.mode = mode;
        this.el.badge.dataset.mode = mode;
        this.el.badge.textContent = mode === "agent" ? "Agent" : "Search";

        const hint = document.getElementById("cc-enter-hint");
        if (hint) hint.textContent = mode === "agent" ? "run" : "open";
    },

    /**
     * Phase 1 heuristic only — replaced by real intent routing in Phase 3
     * (agent_intent_cache + agent_verb_synonyms).
     * Instruction-like = contains a known verb, or is a long multi-word phrase.
     */
    /**
     * Search vs Agent. Verbs come from agent_verb_synonyms via the server,
     * so the dictionary has one source of truth.
     */
    detectMode(q) {
        const text = (q || "").trim().toLowerCase();
        if (!text) return "search";

        const verbs = window.CommandCenterConfig.verbs || [];
        const words = text.split(/\s+/);

        // Any recognised verb makes it an instruction
        if (words.some((w) => verbs.includes(w.replace(/[^a-z]/g, "")))) {
            return "agent";
        }

        // Several words with no reference-looking token is probably an instruction
        const hasRef = words.some(
            (w) => /[a-z]/.test(w) && /\d/.test(w) && w.length >= 4,
        );
        return words.length >= 3 && !hasRef ? "agent" : "search";
    },
    // ── Typing ────────────────────────────────────────────────

    onType() {
        const q = this.el.input.value.trim();

        // Manual typing overrides a prior speech transcript
        this.inputModality = "text";
        this.setMode(this.detectMode(q));

        clearTimeout(this.debounceTimer);

        if (q.length < this.MIN_CHARS) {
            this.rows = [];
            this.activeIndex = -1;
            this.setState("empty");
            return;
        }

        // Agent mode never searches — it waits for Enter
        if (this.mode === "agent") {
            this.rows = [];
            this.activeIndex = -1;
            this.el.stResults.innerHTML = `<p class="cc-hint-text">Press <kbd class="cc-kbd">Enter</kbd> to run this instruction.</p>`;
            this.setState("results");
            return;
        }

        this.debounceTimer = setTimeout(
            () => this.runQuery(q),
            this.DEBOUNCE_MS,
        );
    },

    onInputKey(e) {
        if (e.key === "ArrowDown") {
            e.preventDefault();
            this.move(1);
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            this.move(-1);
        } else if (e.key === "Enter") {
            e.preventDefault();
            this.commit();
        }
    },

    move(delta) {
        if (!this.rows.length) return;
        this.activeIndex =
            (this.activeIndex + delta + this.rows.length) % this.rows.length;
        this.paintActive();
    },

    paintActive() {
        const nodes = this.el.stResults.querySelectorAll(".cc-row");
        nodes.forEach((n, i) =>
            n.classList.toggle("cc-active", i === this.activeIndex),
        );
        const active = nodes[this.activeIndex];
        if (active) active.scrollIntoView({ block: "nearest" });
    },

    commit() {
        if (this.mode === "agent") {
            this.runAgent(this.el.input.value.trim());
            return;
        }
        const row = this.rows[this.activeIndex >= 0 ? this.activeIndex : 0];
        if (row) this.go(row);
    },

    go(row) {
        this.pushRecent(row);
        window.location.href = row.url;
    },

    // ── Query (Phase 1: dummy data, no network) ───────────────

    async runQuery(q) {
        // Cancel any in-flight request so a slow reply can't overwrite a newer one
        if (this.abortController) this.abortController.abort();
        this.abortController = new AbortController();

        this.setState("loading");

        let payload;

        try {
            const url =
                window.CommandCenterConfig.resolveUrl +
                "?q=" +
                encodeURIComponent(q);

            const res = await fetch(url, {
                signal: this.abortController.signal,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            payload = await res.json();
        } catch (e) {
            if (e.name === "AbortError") return; // superseded, do nothing
            this.rows = [];
            this.activeIndex = -1;
            this.el.stNone.querySelector(".cc-hint-text").textContent =
                "Search failed. Check your connection and try again.";
            this.setState("none");
            return;
        }

        this.render(payload.groups || []);
    },

    render(groups) {
        if (!groups.length) {
            this.rows = [];
            this.activeIndex = -1;
            this.el.stNone.querySelector(".cc-hint-text").textContent =
                "No matching records.";
            this.setState("none");
            return;
        }

        this.rows = [];
        let html = "";

        groups.forEach((group) => {
            html += `<p class="cc-section-label">${this.esc(group.label)}</p>`;

            group.items.forEach((item) => {
                const idx = this.rows.length;
                this.rows.push(item);
                html += `
                    <div class="cc-row" data-idx="${idx}"
                         onclick="window.CommandCenter.go(window.CommandCenter.rows[${idx}])">
                        <svg class="cc-row-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="${this.iconPath(group.icon)}" />
                        </svg>
                        <div class="cc-row-main">
                            <div class="cc-row-title ${item.mono ? "cc-mono" : ""}">${this.esc(item.title)}</div>
                            <div class="cc-row-meta">${this.esc(item.meta)}</div>
                        </div>
                    </div>`;
            });
        });

        this.el.stResults.innerHTML = html;
        this.activeIndex = 0;
        this.setState("results");
        this.paintActive();
    },

    /** Server sends an icon key; the SVG path stays client-side. */
    iconPath(key) {
        const paths = {
            box: "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4",
            doc: "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z",
            user: "M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z",
            receipt:
                "M9 14h6m-6-4h6m-8 11l1.5-1.5L10 21l2-2 2 2 1.5-1.5L17 21V5a2 2 0 00-2-2H9a2 2 0 00-2 2v16z",
        };

        return paths[key] || paths.doc;
    },

    // ── Agent thread (Phase 4 replaces this stub) ─────────────

    // ── Agent ─────────────────────────────────────────────────────────────

    async runAgent(instruction, playbook = null) {
        if (!instruction) return;

        const modality = this.inputModality;

        this.renderThread(`
            <p class="cc-thread-you">${this.esc(instruction)}</p>
            <p class="cc-thread-working">Working…</p>
        `);

        let data;
        try {
            const res = await fetch(window.CommandCenterConfig.runUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": window.CommandCenterConfig.csrf,
                },
                body: JSON.stringify({
                    instruction: instruction,
                    modality: modality,
                    playbook: playbook,
                }),
            });

            data = await res.json();

            if (res.status === 429) {
                this.renderThread(
                    this.threadYou(instruction) +
                        `<p class="cc-thread-error">Too many requests. Wait a moment.</p>`,
                );
                return;
            }
        } catch (err) {
            this.renderThread(
                this.threadYou(instruction) +
                    `<p class="cc-thread-error">Could not reach the server.</p>`,
            );
            return;
        }

        if (data.outcome === "blocked") {
            let html = this.threadYou(instruction);
            html += `<p class="cc-thread-flag">Cannot run yet</p>`;

            (data.failures || []).forEach((f) => {
                html += `<p class="cc-thread-reply">${this.esc(f.message)}</p>`;
                if (f.url && f.label) {
                    html += `<p><a class="cc-thread-fix" href="${this.esc(f.url)}">${this.esc(f.label)} →</a></p>`;
                }
            });

            this.renderThread(html);
            return;
        }

        // Bare reference — treat it as a search after all
        if (data.outcome === "search") {
            this.el.input.value = data.query;
            this.setMode("search");
            this.runQuery(data.query);
            return;
        }

        // Not confident enough to act — let the user choose
        if (data.outcome === "suggest") {
            this.renderSuggestions(instruction, data);
            return;
        }

        if (data.outcome === "awaiting_approval") {
            this.renderThread(
                this.threadYou(instruction) +
                    `<p class="cc-thread-flag">Waiting for approval</p>` +
                    `<p class="cc-thread-reply">${this.esc(data.taskLabel || "This task")} is ready but needs approval before it writes anything.</p>`,
            );
            return;
        }

        if (data.outcome === "unresolved") {
            this.renderThread(
                this.threadYou(instruction) +
                    `<p class="cc-thread-error">I can't do that yet. Here's what I can do:</p>` +
                    `<div id="cc-starters"></div>`,
            );
            this.el.starters = document.getElementById("cc-starters");
            this.renderStarters(this.starterList());
            return;
        }

        if (data.outcome === "error" || data.outcome === "failed") {
            this.renderThread(
                this.threadYou(instruction) +
                    `<p class="cc-thread-error">${this.esc(data.message || "That did not work.")}</p>`,
            );
            return;
        }

        this.renderThread(this.threadYou(instruction) + this.threadReply(data));
    },

    threadYou(text) {
        return `<p class="cc-thread-you">${this.esc(text)}</p>`;
    },

    threadReply(data) {
        let html = "";

        if (data.delayed) {
            html += `<p class="cc-thread-flag cc-flag-warn">Delayed</p>`;
        }

        if (data.reply) {
            html +=
                data.replyKind === "list"
                    ? this.replyList(data.reply, data.replyRows)
                    : this.replyConsignment(data.reply);
        }

        const facts = data.facts || {};
        const keys = Object.keys(facts);

        if (keys.length) {
            html += '<dl class="cc-facts">';
            keys.forEach((k) => {
                html += `<dt>${this.esc(k)}</dt><dd${this.factClass(k, facts[k])}>${this.esc(facts[k])}</dd>`;
            });
            html += "</dl>";
        }

        if (data.moreUrl && data.moreLabel) {
            html += `<p class="cc-thread-more"><a href="${this.esc(data.moreUrl)}">${this.esc(data.moreLabel)} →</a></p>`;
        }

        return html;
    },

    /**
     * Three lines doing three jobs: who and what, where it stands, and what
     * is owed next. Flat text buries the third.
     */
    replyConsignment(reply) {
        const lines = reply.split("\n").filter((l) => l.trim() !== "");
        let html = "";

        if (lines.length) {
            html += `<p class="cc-thread-head">${this.esc(lines[0])}</p>`;
        }
        if (lines.length > 1) {
            html += `<p class="cc-thread-sub">${this.esc(lines[1])}</p>`;
        }
        if (lines.length > 2) {
            html += `<p class="cc-thread-action">${this.esc(lines.slice(2).join("\n"))}</p>`;
        }

        return html;
    },

    /** A count, then a sortable table. Prose is the fallback with no rows. */
    replyList(reply, rows) {
        const lines = reply.split("\n").filter((l) => l.trim() !== "");
        let html = lines.length
            ? `<p class="cc-thread-head">${this.esc(lines[0])}</p>`
            : "";

        if (!rows || !rows.length) {
            return html;
        }

        this.listRows = rows;
        this.listSort = { key: "Days", dir: "desc" };

        return html + `<div id="cc-list-wrap">${this.listTable()}</div>`;
    },

    /** Only columns with a value somewhere — a header over 16 blanks is noise. */
    listColumns() {
        return [
            { key: "BL", label: "BL", numeric: false },
            { key: "Consignee", label: "Consignee", numeric: false },
            { key: "Days", label: "Days", numeric: true },
            { key: "Action", label: "Next action", numeric: false },
            { key: "Balance", label: "Balance", numeric: true, money: true },
            { key: "ClaimedBy", label: "With", numeric: false },
        ].filter((c) =>
            this.listRows.some(
                (r) =>
                    r[c.key] !== null &&
                    r[c.key] !== undefined &&
                    r[c.key] !== "",
            ),
        );
    },

    listTable() {
        const cols = this.listColumns();
        const { key, dir } = this.listSort;

        const sorted = [...this.listRows].sort((a, b) => {
            const av = a[key];
            const bv = b[key];

            // Blanks sink regardless of direction
            if (av === null || av === undefined || av === "") return 1;
            if (bv === null || bv === undefined || bv === "") return -1;

            const cmp =
                typeof av === "number" && typeof bv === "number"
                    ? av - bv
                    : String(av).localeCompare(String(bv));

            return dir === "asc" ? cmp : -cmp;
        });

        let html = '<table class="cc-list-table"><thead><tr>';

        cols.forEach((c) => {
            const arrow = c.key === key ? (dir === "asc" ? " ▲" : " ▼") : "";
            const cls = c.numeric ? ' class="cc-num"' : "";
            html += `<th${cls} onclick="window.ccSortList('${c.key}')">${this.esc(c.label)}${arrow}</th>`;
        });

        html += "</tr></thead><tbody>";

        sorted.forEach((r, i) => {
            html += `<tr onclick="window.ccOpenBl(${i})">`;
            cols.forEach((c) => {
                const cls = c.numeric ? ' class="cc-num"' : "";
                const val = r[c.key];
                const blank = val === null || val === undefined || val === "";
                html += `<td${cls}>${this.esc(
                    blank ? "—" : c.money ? this.money(val) : val,
                )}</td>`;
            });
            html += "</tr>";
        });

        this.listSorted = sorted;

        return html + "</tbody></table>";
    },

    /** Identifiers read better in mono; status carries its own colour. */
    factClass(label, value) {
        if (label === "Status") {
            const tone = this.statusTone(String(value));
            return tone ? ` class="cc-status cc-status-${tone}"` : "";
        }

        return ["BL", "Vessel", "Containers", "House BLs"].includes(label)
            ? ' class="cc-mono"'
            : "";
    },

    /** Mirrors the consignment badge palette used across the app. */
    statusTone(label) {
        const map = {
            Cleared: "gray",
            "Gated Out": "amber",
            Pending: "purple",
            "Not Arrived": "blue",
        };

        return map[label] || null;
    },

    /**
     * Below the confidence floor. The pick is the training signal — the server
     * caches the mapping only after the chosen task actually runs.
     */
    renderSuggestions(instruction, data) {
        this.suggestions = data.suggestions || [];

        if (!this.suggestions.length) {
            this.renderThread(
                this.threadYou(instruction) +
                    `<p class="cc-thread-error">I don't know how to do that yet.</p>`,
            );
            return;
        }

        let html = this.threadYou(instruction);
        html += `<p class="cc-thread-flag">${this.esc(data.message || "Did you mean one of these?")}</p>`;

        this.suggestions.forEach((s, i) => {
            html += `
                <div class="cc-row cc-suggest" data-index="${i}" role="button" tabindex="0">
                    <div class="cc-row-body">
                        <div class="cc-row-title">${this.esc(s.title)}</div>
                    </div>
                </div>`;
        });

        this.renderThread(html);

        this.el.stThread.querySelectorAll(".cc-suggest").forEach((row) => {
            const pick = () => {
                const choice = this.suggestions[Number(row.dataset.index)];
                if (choice) this.runAgent(instruction, choice.key);
            };

            row.addEventListener("click", pick);
            row.addEventListener("keydown", (e) => {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    pick();
                }
            });
        });
    },

    renderThread(html) {
        this.el.stThread.innerHTML = `<div class="cc-thread">${html}</div>`;
        this.setState("thread");
    },

    // ── Recents (localStorage) ────────────────────────────────

    getRecents() {
        try {
            return JSON.parse(localStorage.getItem(this.RECENTS_KEY)) || [];
        } catch (e) {
            return [];
        }
    },

    pushRecent(row) {
        let list = this.getRecents().filter((r) => r.title !== row.title);
        list.unshift({
            title: row.title,
            meta: row.meta,
            url: row.url,
            mono: !!row.mono,
        });
        list = list.slice(0, this.RECENTS_MAX);
        try {
            localStorage.setItem(this.RECENTS_KEY, JSON.stringify(list));
        } catch (e) {
            /* quota / private mode — non-fatal */
        }
    },

    renderStarters(items) {
        if (!this.el.starters) return;

        this.starters = items;

        this.el.starters.innerHTML = items
            .map(
                (s, i) => `
            <button type="button" class="cc-starter" onclick="window.ccStarter(${i})">
                <svg class="cc-starter-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="${this.iconPath(s.icon)}" />
                </svg>
                <span class="cc-starter-title">${this.esc(s.title)}</span>
                <span class="cc-starter-mark">${s.run ? "↵" : "…"}</span>
            </button>`,
            )
            .join("");
    },

    /** What the agent can actually do today. Grows as playbooks land. */
    starterList() {
        return [
            {
                title: "Status of a consignment",
                fill: "status of ",
                icon: "box",
            },
            {
                title: "Breakdown of a manifest",
                fill: "breakdown of ",
                icon: "doc",
            },
            {
                title: "Overdue consignments",
                run: "what is overdue",
                icon: "box",
            },
            {
                title: "Not yet disbursed",
                run: "what has not been disbursed",
                icon: "receipt",
            },
            {
                title: "Not yet invoiced",
                run: "what has not been invoiced",
                icon: "doc",
            },
            {
                title: "Unconfirmed type",
                run: "show unconfirmed type",
                icon: "box",
            },
            {
                title: "Clients owing money",
                run: "who owes us money",
                icon: "user",
            },
        ];
    },

    renderRecents() {
        const list = this.getRecents();
        this.el.noRecents.hidden = list.length > 0;

        this.el.recents.innerHTML = list
            .map(
                (r, i) => `
            <div class="cc-row" onclick="window.CommandCenter.goRecent(${i})">
                <svg class="cc-row-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="cc-row-main">
                    <div class="cc-row-title ${r.mono ? "cc-mono" : ""}">${this.esc(r.title)}</div>
                    <div class="cc-row-meta">${this.esc(r.meta)}</div>
                </div>
            </div>`,
            )
            .join("");
    },

    goRecent(i) {
        const r = this.getRecents()[i];
        if (r) window.location.href = r.url;
    },

    // ── Speech ────────────────────────────────────────────────

    setupSpeech() {
        const SR = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!SR) {
            this.el.micBtn.style.display = "none"; // Firefox / unsupported
            return;
        }

        const r = new SR();
        r.lang = this.SPEECH_LANG;
        r.continuous = false;
        r.interimResults = true;

        r.onstart = () => {
            this.listening = true;
            this.el.micBtn.dataset.listening = "1";
            this.micState(
                "listening",
                "Listening — tap the mic again to stop.",
            );
        };

        r.onresult = (e) => {
            let text = "";
            for (let i = e.resultIndex; i < e.results.length; i++) {
                text += e.results[i][0].transcript;
            }
            // Transcript lands in the field for review — never auto-submits
            this.el.input.value = text;
            this.inputModality = "speech";
            this.setMode(this.detectMode(text));
        };

        r.onerror = (e) => {
            const msg =
                {
                    "not-allowed":
                        "Microphone blocked. Allow access in your browser settings.",
                    "service-not-allowed":
                        "Microphone blocked. Allow access in your browser settings.",
                    "no-speech": "No speech detected. Try again.",
                    "audio-capture": "No microphone found.",
                    network: "Speech service unreachable.",
                }[e.error] || "Speech input failed. Try typing instead.";
            this.micState("error", msg);
        };

        r.onend = () => {
            this.listening = false;
            this.el.micBtn.dataset.listening = "0";
            if (this.el.micStatus.dataset.state === "listening") {
                this.micState("idle", "");
            }
            // Fire the search now that dictation has settled
            if (this.el.input.value.trim().length >= this.MIN_CHARS) {
                this.onType();
            }
        };

        this.recognition = r;
    },

    toggleMic() {
        if (!this.recognition) return;
        this.listening ? this.stopMic() : this.startMic();
    },

    startMic() {
        this.micState("idle", "");
        try {
            this.recognition.start();
        } catch (e) {
            this.micState("error", "Could not start the microphone.");
        }
    },

    stopMic() {
        try {
            this.recognition.stop();
        } catch (e) {
            /* already stopped */
        }
    },

    micState(state, text) {
        this.el.micStatus.dataset.state = state;
        this.el.micText.textContent = text;
    },

    // ── Util ──────────────────────────────────────────────────

    esc(s) {
        const d = document.createElement("div");
        d.textContent = s === null || s === undefined ? "" : String(s);
        return d.innerHTML;
    },

    money(value) {
        return (
            "GH₵ " +
            Number(value).toLocaleString("en-GH", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    },
};

/* ── Global keyboard dispatcher ── */
document.addEventListener("keydown", function (e) {
    // Ctrl+K / Cmd+K — open (never toggles), fires regardless of focus
    if ((e.ctrlKey || e.metaKey) && (e.key === "k" || e.key === "K")) {
        e.preventDefault();
        window.CommandCenter.show();
        return;
    }

    // Escape — only when Command Center is the top layer
    if (e.key === "Escape" && window.CommandCenter.open) {
        e.preventDefault();
        window.CommandCenter.close();
    }
});

document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => window.CommandCenter.init(), 0);
});

let bellLoaded = false;

function toggleBell() {
    const menu = document.getElementById("bell-menu");
    const open = menu.style.display !== "none";

    menu.style.display = open ? "none" : "block";

    if (!open && !bellLoaded) loadBell();
}

function loadBell() {
    if (!window.BELL_URL) return;

    fetch(window.BELL_URL, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
    })
        .then((r) => r.json())
        .then((d) => {
            bellLoaded = true;
            renderBell(d);
        })
        .catch(() => {
            const body = document.getElementById("bell-body");
            if (body) body.textContent = "Could not load.";
        });
}

function renderBell(d) {
    const rows = [];

    if (d.unclaimed)
        rows.push(
            bellRow(d.stallUrl, d.unclaimed + " need someone", "#b91c1c"),
        );
    if (d.quiet)
        rows.push(
            bellRow(d.stallUrl, d.quiet + " claimed but gone quiet", "#b45309"),
        );
    if (d.resets)
        rows.push(
            bellRow(
                d.resetUrl,
                d.resets + " password reset request(s)",
                "var(--text-primary)",
            ),
        );

    const body = document.getElementById("bell-body");
    if (body)
        body.innerHTML = rows.length
            ? rows.join("")
            : "Nothing needs attention.";

    const total = (d.unclaimed || 0) + (d.quiet || 0) + (d.resets || 0);
    const badge = document.getElementById("bell-badge");
    if (badge) {
        badge.textContent = total;
        badge.style.display = total ? "flex" : "none";
    }
}

function bellRow(url, text, colour) {
    return (
        '<a href="' +
        url +
        '" style="display:block;padding:8px 0;color:' +
        colour +
        ';font-size:0.8rem;text-decoration:none;border-bottom:1px solid var(--border-color);">' +
        text +
        "</a>"
    );
}

document.addEventListener("click", function (e) {
    const wrap = document.getElementById("bell-wrapper");
    const menu = document.getElementById("bell-menu");
    if (wrap && menu && !wrap.contains(e.target)) {
        menu.style.display = "none";
    }
});

window.toggleBell = toggleBell;

/** Re-sort in place. The same column toggles direction. */
window.ccSortList = function (key) {
    const cc = window.CommandCenter;
    const wrap = document.getElementById("cc-list-wrap");

    if (!cc || !wrap) return;

    cc.listSort =
        cc.listSort.key === key
            ? { key, dir: cc.listSort.dir === "asc" ? "desc" : "asc" }
            : { key, dir: key === "Days" ? "desc" : "asc" };

    wrap.innerHTML = cc.listTable();
};

/** A row is a question waiting to be asked. */
window.ccOpenBl = function (index) {
    const cc = window.CommandCenter;
    const row = cc?.listSorted?.[index];

    if (!row?.BL) return;

    const instruction = "status of " + row.BL;
    cc.el.input.value = instruction;
    cc.setMode("agent");
    cc.runAgent(instruction);
};

window.ccStarter = function (i) {
    const cc = window.CommandCenter;
    const s = cc?.starters?.[i];

    if (!s) return;

    if (s.run) {
        cc.el.input.value = s.run;
        cc.setMode("agent");
        cc.runAgent(s.run);
        return;
    }

    cc.el.input.value = s.fill;
    cc.setMode("agent");
    cc.el.input.focus();
};

setTimeout(loadBell, 0);
