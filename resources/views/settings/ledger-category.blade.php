@extends('layouts.app')

@section('title', 'Ledger Category')
@section('page-title', 'Ledger Category Setup')

@section('content')

<div style="display: flex; gap: 1.5rem; height: calc(100vh - 120px);">

    {{-- ── Left Panel: Forms ── --}}
    <div style="width: 320px; flex-shrink: 0; display: flex; flex-direction: column; gap: 1rem;">

        {{-- Add New Category --}}
        <div class="card">
            <p class="form-title">New Category</p>
            <p class="form-subtitle">Add a new parent category group</p>

            <div class="form-group">
                <label class="form-label">Category Name</label>
                <input
                    type="text"
                    id="category-name-input"
                    placeholder="e.g. OPERATING RESERVE"
                    maxlength="100"
                    class="form-input">
                <p id="category-name-error" class="form-error"></p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Class</label>
                    <select id="category-class-input" class="form-input">
                        <option value="">Select</option>
                        <option value="Dr">Dr (Debit)</option>
                        <option value="Cr">Cr (Credit)</option>
                    </select>
                    <p id="category-class-error" class="form-error"></p>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Type</label>
                    <select id="category-type-input" class="form-input">
                        <option value="">Select</option>
                        <option value="GL">GL</option>
                        <option value="INCOME">Income</option>
                        <option value="EXPENDITURE">Expenditure</option>
                    </select>
                    <p id="category-type-error" class="form-error"></p>
                </div>
            </div>

            <button onclick="addCategory()" id="add-category-btn" class="btn-primary" style="margin-top: 1rem;">
                Add Category
            </button>

            <p id="category-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>
        </div>

        {{-- Add New SubCategory --}}
        <div class="card" style="flex: 1;">
            <p class="form-title">New Subcategory</p>
            <p class="form-subtitle">Add a subcategory under an existing category</p>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select id="subcategory-category-input" class="form-input" onchange="onCategoryChange()">
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                    <option
                        value="{{ $category->CategoryID }}"
                        data-name="{{ $category->CategoryName }}"
                        data-class="{{ $category->Class }}"
                        data-type="{{ $category->Type }}">
                        {{ $category->CategoryName }}
                    </option>
                    @endforeach
                </select>
                <p id="subcategory-category-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Subcategory Name</label>
                <input
                    type="text"
                    id="subcategory-name-input"
                    placeholder="e.g. CURRENT ASSET"
                    maxlength="100"
                    class="form-input">
                <p id="subcategory-name-error" class="form-error"></p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Class</label>
                    <select id="subcategory-class-input" class="form-input">
                        <option value="">Select</option>
                        <option value="Dr">Dr (Debit)</option>
                        <option value="Cr">Cr (Credit)</option>
                    </select>
                    <p id="subcategory-class-error" class="form-error"></p>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Type</label>
                    <select id="subcategory-type-input" class="form-input">
                        <option value="">Select</option>
                        <option value="GL">GL</option>
                        <option value="INCOME">Income</option>
                        <option value="EXPENDITURE">Expenditure</option>
                    </select>
                    <p id="subcategory-type-error" class="form-error"></p>
                </div>
            </div>

            <button onclick="addSubCategory()" id="add-subcategory-btn" class="btn-primary" style="margin-top: 1rem;">
                Add Subcategory
            </button>

            <p id="subcategory-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>
        </div>

    </div>

    {{-- ── Right Panel: Existing Subcategories ── --}}
    <div style="flex: 1; min-width: 0;">
        <div class="card h-full" style="display: flex; flex-direction: column;">

            {{-- Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-shrink: 0;">
                <div>
                    <p class="form-title">Existing Subcategories</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        <span id="active-count">{{ $activeSubCategories->count() }}</span> active subcategories
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    {{-- Search --}}
                    <div style="position: relative;">
                        <svg style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--text-muted);"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Search subcategories..."
                            oninput="filterSubCategories()"
                            class="form-input"
                            style="padding-left: 32px; width: 220px;">
                    </div>

                    {{-- Show inactive toggle --}}
                    <button
                        id="toggle-inactive-btn"
                        onclick="toggleInactive()"
                        style="padding: 8px 14px; border-radius: 8px; font-size: 0.75rem; font-weight: 500; cursor: pointer; border: 1.5px solid var(--border-color); background: var(--content-bg); color: var(--text-muted); transition: all 0.15s;">
                        Show Inactive
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div style="flex: 1; overflow-y: auto; min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Subcategory Name</th>
                            <th style="width: 160px;">Category</th>
                            <th style="width: 60px;">Class</th>
                            <th style="width: 100px;">Type</th>
                            <th style="width: 80px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="subcategories-table-body">

                        {{-- Active subcategories --}}
                        @forelse($activeSubCategories as $sub)
                        <tr class="subcategory-row active-row"
                            data-name="{{ strtolower($sub->SubCategoryName) }} {{ strtolower($sub->CategoryName) }}">
                            <td class="td-mono">{{ $sub->SubCategoryID }}</td>
                            <td>
                                {{-- Inline edit --}}
                                <span
                                    class="subcategory-name-display"
                                    style="font-weight: 500; cursor: pointer; color: var(--text-primary);"
                                    onmouseover="this.style.color='#16a34a'"
                                    onmouseout="this.style.color='var(--text-primary)'"
                                    onclick="startEdit(this, {{ $sub->SubCategoryID }})"
                                    title="Click to edit">
                                    {{ $sub->SubCategoryName }}
                                </span>
                                <input
                                    type="text"
                                    class="subcategory-name-input form-input"
                                    style="display: none; width: 180px; padding: 6px 10px;"
                                    value="{{ $sub->SubCategoryName }}"
                                    data-original="{{ $sub->SubCategoryName }}"
                                    data-id="{{ $sub->SubCategoryID }}"
                                    onkeydown="handleEditKey(event, this)"
                                    onblur="saveEdit(this)">
                            </td>
                            <td class="td-muted">{{ $sub->CategoryName }}</td>
                            <td>
                                <span style="font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px;
                                    background: {{ $sub->Class === 'Dr' ? 'rgba(59,130,246,0.1)' : 'rgba(22,163,74,0.1)' }};
                                    color: {{ $sub->Class === 'Dr' ? '#3b82f6' : '#16a34a' }};">
                                    {{ $sub->Class }}
                                </span>
                            </td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $sub->Type }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="deactivateSubCategory({{ $sub->SubCategoryID }}, this)"
                                    class="btn-icon btn-icon-danger"
                                    title="Deactivate">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                No active subcategories found. Add one using the form on the left.
                            </td>
                        </tr>
                        @endforelse

                        {{-- Inactive subcategories --}}
                        @foreach($inactiveSubCategories as $sub)
                        <tr class="subcategory-row inactive-row"
                            data-name="{{ strtolower($sub->SubCategoryName) }} {{ strtolower($sub->CategoryName) }}"
                            style="display: none; opacity: 0.6;">
                            <td class="td-mono">{{ $sub->SubCategoryID }}</td>
                            <td>
                                <span style="font-weight: 500; text-decoration: line-through; color: var(--text-muted);">
                                    {{ $sub->SubCategoryName }}
                                </span>
                                <span style="margin-left: 8px; font-size: 0.6rem; padding: 2px 6px; border-radius: 9999px; background: rgba(239,68,68,0.1); color: #ef4444; font-weight: 600;">
                                    INACTIVE
                                </span>
                            </td>
                            <td class="td-muted">{{ $sub->CategoryName }}</td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $sub->Class }}</td>
                            <td class="td-muted" style="font-size: 0.75rem;">{{ $sub->Type }}</td>
                            <td style="text-align: center;">
                                <button
                                    onclick="restoreSubCategory({{ $sub->SubCategoryID }}, this)"
                                    class="btn-icon btn-icon-success"
                                    title="Restore">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    let showingInactive = false;

    // ── Category auto-fill on selection ──
    function onCategoryChange() {
        const select   = document.getElementById('subcategory-category-input');
        const selected = select.options[select.selectedIndex];

        if (!selected.value) return;

        const cls  = selected.getAttribute('data-class');
        const type = selected.getAttribute('data-type');

        // Auto-fill Class
        const classSelect = document.getElementById('subcategory-class-input');
        for (let opt of classSelect.options) {
            if (opt.value === cls) { opt.selected = true; break; }
        }

        // Auto-fill Type
        const typeSelect = document.getElementById('subcategory-type-input');
        for (let opt of typeSelect.options) {
            if (opt.value === type) { opt.selected = true; break; }
        }
    }

    // ── Filter subcategories ──
    function filterSubCategories() {
        const query = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.subcategory-row').forEach(row => {
            const name    = row.getAttribute('data-name');
            const visible = name.includes(query);
            if (row.classList.contains('inactive-row')) {
                row.style.display = showingInactive && visible ? '' : 'none';
            } else {
                row.style.display = visible ? '' : 'none';
            }
        });
    }

    // ── Toggle inactive ──
    function toggleInactive() {
        showingInactive = !showingInactive;
        const btn = document.getElementById('toggle-inactive-btn');
        document.querySelectorAll('.inactive-row').forEach(row => {
            row.style.display = showingInactive ? '' : 'none';
        });
        btn.style.background  = showingInactive ? 'rgba(22,163,74,0.08)' : 'var(--content-bg)';
        btn.style.borderColor = showingInactive ? 'rgba(22,163,74,0.3)'  : 'var(--border-color)';
        btn.style.color       = showingInactive ? '#16a34a'               : 'var(--text-muted)';
        btn.textContent       = showingInactive ? 'Hide Inactive'         : 'Show Inactive';
    }

    // ── Add Category ──
    function addCategory() {
        const nameInput  = document.getElementById('category-name-input');
        const classInput = document.getElementById('category-class-input');
        const typeInput  = document.getElementById('category-type-input');
        const errorName  = document.getElementById('category-name-error');
        const errorClass = document.getElementById('category-class-error');
        const errorType  = document.getElementById('category-type-error');
        const successEl  = document.getElementById('category-success');
        const btn        = document.getElementById('add-category-btn');

        // Reset
        [errorName, errorClass, errorType, successEl].forEach(el => el.classList.remove('visible'));

        let valid = true;
        if (!nameInput.value.trim()) {
            errorName.textContent = 'Category name is required.';
            errorName.classList.add('visible');
            valid = false;
        }
        if (!classInput.value) {
            errorClass.textContent = 'Class is required.';
            errorClass.classList.add('visible');
            valid = false;
        }
        if (!typeInput.value) {
            errorType.textContent = 'Type is required.';
            errorType.classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("settings.ledger-category.store-category") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                CategoryName: nameInput.value.trim(),
                Class:        classInput.value,
                Type:         typeInput.value,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Add new category to subcategory dropdown
                const select = document.getElementById('subcategory-category-input');
                const option = document.createElement('option');
                option.value                      = data.CategoryID;
                option.textContent                = data.CategoryName;
                option.setAttribute('data-name',  data.CategoryName);
                option.setAttribute('data-class', data.Class);
                option.setAttribute('data-type',  data.Type);
                select.appendChild(option);

                nameInput.value  = '';
                classInput.value = '';
                typeInput.value  = '';

                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => successEl.classList.remove('visible'), 3000);
            } else {
                errorName.textContent = data.message ?? 'Failed to add category.';
                errorName.classList.add('visible');
            }
        })
        .catch(() => {
            errorName.textContent = 'Something went wrong. Please try again.';
            errorName.classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Category';
            btn.disabled    = false;
        });
    }

    // ── Add SubCategory ──
    function addSubCategory() {
        const categoryInput = document.getElementById('subcategory-category-input');
        const nameInput     = document.getElementById('subcategory-name-input');
        const classInput    = document.getElementById('subcategory-class-input');
        const typeInput     = document.getElementById('subcategory-type-input');
        const errorCat      = document.getElementById('subcategory-category-error');
        const errorName     = document.getElementById('subcategory-name-error');
        const errorClass    = document.getElementById('subcategory-class-error');
        const errorType     = document.getElementById('subcategory-type-error');
        const successEl     = document.getElementById('subcategory-success');
        const btn           = document.getElementById('add-subcategory-btn');

        // Reset
        [errorCat, errorName, errorClass, errorType, successEl].forEach(el => el.classList.remove('visible'));

        let valid = true;
        if (!categoryInput.value) {
            errorCat.textContent = 'Please select a category.';
            errorCat.classList.add('visible');
            valid = false;
        }
        if (!nameInput.value.trim()) {
            errorName.textContent = 'Subcategory name is required.';
            errorName.classList.add('visible');
            valid = false;
        }
        if (!classInput.value) {
            errorClass.textContent = 'Class is required.';
            errorClass.classList.add('visible');
            valid = false;
        }
        if (!typeInput.value) {
            errorType.textContent = 'Type is required.';
            errorType.classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        const selected     = categoryInput.options[categoryInput.selectedIndex];
        const categoryName = selected.getAttribute('data-name');

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("settings.ledger-category.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                CategoryID:      categoryInput.value,
                CategoryName:    categoryName,
                SubCategoryName: nameInput.value.trim(),
                Class:           classInput.value,
                Type:            typeInput.value,
            }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                nameInput.value = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                errorName.textContent = data.message ?? 'Failed to add subcategory.';
                errorName.classList.add('visible');
            }
        })
        .catch(() => {
            errorName.textContent = 'Something went wrong. Please try again.';
            errorName.classList.add('visible');
        })
        .finally(() => {
            btn.textContent = 'Add Subcategory';
            btn.disabled    = false;
        });
    }

    // ── Inline edit ──
    function startEdit(span, id) {
        const input         = span.nextElementSibling;
        span.style.display  = 'none';
        input.style.display = 'inline-block';
        input.focus();
        input.select();
    }

    function handleEditKey(e, input) {
        if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { cancelEdit(input); }
    }

    function cancelEdit(input) {
        const span          = input.previousElementSibling;
        input.value         = input.getAttribute('data-original');
        input.style.display = 'none';
        span.style.display  = '';
    }

    function saveEdit(input) {
        const span     = input.previousElementSibling;
        const id       = input.getAttribute('data-id');
        const original = input.getAttribute('data-original');
        const newName  = input.value.trim();

        if (!newName || newName === original) {
            cancelEdit(input);
            return;
        }

        fetch(`/settings/ledger-category/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ SubCategoryName: newName }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                span.textContent = data.SubCategoryName;
                input.setAttribute('data-original', data.SubCategoryName);
                input.value = data.SubCategoryName;
            } else {
                cancelEdit(input);
                alert(data.message ?? 'Failed to update.');
            }
        })
        .catch(() => {
            cancelEdit(input);
            alert('Something went wrong. Please try again.');
        })
        .finally(() => {
            input.style.display = 'none';
            span.style.display  = '';
        });
    }

    // ── Deactivate ──
    function deactivateSubCategory(id, btn) {
        if (!confirm('Deactivate this subcategory?')) return;

        fetch(`/settings/ledger-category/${id}/deactivate`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.closest('tr').remove();
                const countEl       = document.getElementById('active-count');
                countEl.textContent = parseInt(countEl.textContent) - 1;
            } else {
                alert(data.message ?? 'Failed to deactivate.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }

    // ── Restore ──
    function restoreSubCategory(id, btn) {
        fetch(`/settings/ledger-category/${id}/restore`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message ?? 'Failed to restore.');
            }
        })
        .catch(() => alert('Something went wrong. Please try again.'));
    }
</script>
@endpush