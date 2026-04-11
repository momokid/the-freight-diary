/**
 * SearchDropdown — Reusable secure typeahead search component
 * Assigned directly to window — no ES module export needed.
 */
window.SearchDropdown = class SearchDropdown {
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

        if (!this.inputEl || !this.dropdownEl) {
            console.warn(
                `SearchDropdown: element not found — inputId="${options.inputId}"`,
            );
            return;
        }

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

        fetch(`${this.url}${sep}q=${encodeURIComponent(q)}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then((res) => {
                if (res.status === 429) {
                    console.warn("SearchDropdown: rate limit hit.");
                    return [];
                }
                return res.json();
            })
            .then((data) => {
                if (!data || !data.length) {
                    this._close();
                    return;
                }
                this._render(data);
            })
            .catch(() => this._close());
    }

    _render(items) {
        while (this.dropdownEl.firstChild) {
            this.dropdownEl.removeChild(this.dropdownEl.firstChild);
        }

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
window.SearchDropdown = SearchDropdown;
document.dispatchEvent(new Event("search-dropdown-ready"));
