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
        consignment: ["/consignments"],
        invoice: ["/invoice"],
        payment: ["/payment"],
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
