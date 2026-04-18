/**
 * Freight Diary v2.0 — Layout JavaScript
 * Handles: theme toggle, sidebar toggle, submenus, user dropdown, active submenu detection
 * Location: public/js/layout.js
 */

// reveal page after CSS loads — prevents FOUC
document.addEventListener("DOMContentLoaded", function () {});

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

// ── Submenus ──
function toggleSubmenu(key) {
    const submenu = document.getElementById("submenu-" + key);
    const arrow = document.getElementById("arrow-" + key);
    if (!submenu) return;
    const isOpen = submenu.classList.contains("open");
    submenu.classList.toggle("open", !isOpen);
    submenu.classList.toggle("closed", isOpen);
    if (arrow) arrow.style.transform = isOpen ? "" : "rotate(180deg)";
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
        accounting: ["/accounting"],
        disbursement: ["/disbursement"],
        edit: ["/edit"],
        reports: ["/reports"],
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
