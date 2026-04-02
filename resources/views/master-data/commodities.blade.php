@extends('layouts.app')

@section('title', 'Commodity Types')
@section('page-title', 'Commodity Type Management')

@section('content')

<div class="flex gap-6" style="height: calc(100vh - 90px);">

    {{-- ── Left Panel ── --}}
    <div class="flex-shrink-0" style="width: 300px;">
        <div class="card h-full flex flex-col overflow-y-auto">

            <p class="form-title">New Category</p>
            <p class="form-subtitle">Add a new commodity category</p>

            <div class="form-group">
                <label class="form-label">Category Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="category-name-input" placeholder="e.g. General Cargo" maxlength="50" class="form-input">
                <p id="category-name-error" class="form-error"></p>
            </div>

            <button onclick="addCategory()" id="category-add-btn" class="btn-primary">Add Category</button>
            <p id="category-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

            <div style="border-top: 1px solid var(--border-color); margin: 1.5rem 0;"></div>

            <p class="form-title">New Commodity Type</p>
            <p class="form-subtitle">Add a type under an existing category</p>

            <div class="form-group">
                <label class="form-label">Category <span style="color: #ef4444;">*</span></label>
                <select id="type-category-input" class="form-input">
                    <option value="">Select category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->ID }}">{{ $category->CategoryName }}</option>
                    @endforeach
                </select>
                <p id="type-category-error" class="form-error"></p>
            </div>

            <div class="form-group">
                <label class="form-label">Type Name <span style="color: #ef4444;">*</span></label>
                <input type="text" id="type-name-input" placeholder="e.g. Electronics" class="form-input">
                <p id="type-name-error" class="form-error"></p>
            </div>

            <button onclick="addType()" id="type-add-btn" class="btn-primary">Add Type</button>
            <p id="type-success" class="form-success" style="margin-top: 8px; text-align: center;"></p>

        </div>
    </div>

    {{-- ── Right Panel ── --}}
    <div class="flex-1 min-w-0">
        <div class="card h-full flex flex-col">

            <div class="flex items-center justify-between mb-4 flex-shrink-0">
                <div>
                    <p class="form-title">Existing Commodity Types</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                        {{ $categories->sum(fn($c) => $c->types->count()) }} types across {{ $categories->count() }} categories
                    </p>
                </div>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-muted);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="search-input" placeholder="Search types..." oninput="filterRows()" class="form-input" style="padding-left: 32px; width: 200px;">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto" style="min-height: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 160px;">Category</th>
                            <th>Type Name</th>
                            <th style="width: 60px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            @forelse($category->types as $type)
                            <tr class="type-row" data-name="{{ strtolower($type->TypeName) }} {{ strtolower($category->CategoryName) }}">
                                <td>
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: rgba(59,130,246,0.1); color: #3b82f6;">
                                        {{ $category->CategoryName }}
                                    </span>
                                </td>
                                <td style="font-weight: 500; color: var(--text-primary);">{{ $type->TypeName }}</td>
                                <td style="text-align: center;">
                                    <button onclick="deleteType({{ $type->TypeID }}, this)" class="btn-icon btn-icon-danger" title="Delete">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>
                                    <span style="font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; background: rgba(59,130,246,0.1); color: #3b82f6;">
                                        {{ $category->CategoryName }}
                                    </span>
                                </td>
                                <td colspan="2" style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No types yet</td>
                            </tr>
                            @endforelse
                        @empty
                        <tr><td colspan="3" style="padding: 2rem; text-align: center; color: var(--text-muted);">No categories found.</td></tr>
                        @endforelse
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

    function filterRows() {
        const q = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.type-row').forEach(row => {
            row.style.display = row.getAttribute('data-name').includes(q) ? '' : 'none';
        });
    }

    function addCategory() {
        const nameEl    = document.getElementById('category-name-input');
        const errorEl   = document.getElementById('category-name-error');
        const successEl = document.getElementById('category-success');
        const btn       = document.getElementById('category-add-btn');

        errorEl.classList.remove('visible');
        successEl.classList.remove('visible');

        if (!nameEl.value.trim()) {
            errorEl.textContent = 'Category name is required.';
            errorEl.classList.add('visible');
            return;
        }

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("master-data.commodities.category.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ CategoryName: nameEl.value.trim() }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                nameEl.value = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                // Add new option to type category dropdown
                const select = document.getElementById('type-category-input');
                const option = document.createElement('option');
                option.value       = data.ID;
                option.textContent = data.CategoryName;
                select.appendChild(option);
                setTimeout(() => successEl.classList.remove('visible'), 3000);
            } else {
                errorEl.textContent = data.message ?? 'Failed to add category.';
                errorEl.classList.add('visible');
            }
        })
        .catch(() => { errorEl.textContent = 'Something went wrong.'; errorEl.classList.add('visible'); })
        .finally(() => { btn.textContent = 'Add Category'; btn.disabled = false; });
    }

    function addType() {
        const categoryEl = document.getElementById('type-category-input');
        const nameEl     = document.getElementById('type-name-input');
        const successEl  = document.getElementById('type-success');
        const btn        = document.getElementById('type-add-btn');

        ['type-category', 'type-name'].forEach(f => document.getElementById(f + '-error').classList.remove('visible'));
        successEl.classList.remove('visible');

        let valid = true;
        if (!categoryEl.value) {
            document.getElementById('type-category-error').textContent = 'Please select a category.';
            document.getElementById('type-category-error').classList.add('visible');
            valid = false;
        }
        if (!nameEl.value.trim()) {
            document.getElementById('type-name-error').textContent = 'Type name is required.';
            document.getElementById('type-name-error').classList.add('visible');
            valid = false;
        }
        if (!valid) return;

        btn.textContent = 'Adding...';
        btn.disabled    = true;

        fetch('{{ route("master-data.commodities.type.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ CategoryID: categoryEl.value, TypeName: nameEl.value.trim() }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                nameEl.value = '';
                successEl.textContent = data.message;
                successEl.classList.add('visible');
                setTimeout(() => location.reload(), 800);
            } else {
                document.getElementById('type-name-error').textContent = data.message ?? 'Failed to add type.';
                document.getElementById('type-name-error').classList.add('visible');
            }
        })
        .catch(() => { document.getElementById('type-name-error').textContent = 'Something went wrong.'; document.getElementById('type-name-error').classList.add('visible'); })
        .finally(() => { btn.textContent = 'Add Type'; btn.disabled = false; });
    }

    function deleteType(id, btn) {
        if (!confirm('Delete this commodity type? This cannot be undone.')) return;
        fetch(`/master-data/commodities/type/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(res => res.json())
        .then(data => { if (data.success) btn.closest('tr').remove(); else alert(data.message); })
        .catch(() => alert('Something went wrong.'));
    }
</script>
@endpush