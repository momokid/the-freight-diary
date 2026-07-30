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
    },

    /**
     * Phase 1 heuristic only — replaced by real intent routing in Phase 3
     * (agent_intent_cache + agent_verb_synonyms).
     * Instruction-like = contains a known verb, or is a long multi-word phrase.
     */
    detectMode(q) {
        const verbs =
            /\b(draft|create|register|prepare|generate|send|show|list|what|how|check|update|make|add|breakdown|disburse)\b/i;
        const words = q.trim().split(/\s+/).length;
        return verbs.test(q) || words >= 4 ? "agent" : "search";
    },

    // ── Typing ────────────────────────────────────────────────

    onType() {
        const q = this.el.input.value.trim();

        // Manual typing overrides a prior speech transcript
        this.inputModality = "text";
        this.setMode(this.detectMode(q));

        clearTimeout(this.debounceTimer);

        if (q.length < this.MIN_CHARS) {
            if (this.abortController) this.abortController.abort();
            this.rows = [];
            this.activeIndex = -1;
            this.setState("empty");
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
            this.stubThread(this.el.input.value.trim());
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

    stubThread(q) {
        this.el.stThread.innerHTML = `
            <div style="padding:16px;">
                <p style="font-size:12px;font-weight:700;color:var(--text-muted);
                          text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">
                    You said
                </p>
                <p style="font-size:14px;color:var(--text-primary);margin-bottom:16px;">
                    ${this.esc(q)}
                </p>
                <p style="font-size:13px;color:var(--text-muted);">
                    Agent execution arrives in Phase 4. Input modality:
                    <strong>${this.inputModality}</strong>.
                </p>
            </div>`;
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
