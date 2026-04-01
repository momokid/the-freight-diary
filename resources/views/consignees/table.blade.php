{{-- Active consignees --}}
@forelse($consignees as $consignee)
<tr>
    <td class="td-mono">{{ $consignee->ConsigneeID }}</td>
    <td style="font-weight: 500; color: var(--text-primary);">
        {{ $consignee->FullName }}
    </td>
    <td class="td-muted">{{ $consignee->TelNo }}</td>
    <td class="td-muted" style="font-size: 0.8rem;">
        {{ $consignee->Address1 }}
        @if($consignee->Address2), {{ $consignee->Address2 }}@endif
        @if($consignee->Address3), {{ $consignee->Address3 }}@endif
    </td>
    <td style="text-align: center;">
        <div class="flex items-center justify-center gap-1">
            <button
                onclick="openEditModal({{ $consignee->ConsigneeID }}, '{{ addslashes($consignee->FullName) }}', '{{ addslashes($consignee->TelNo) }}', '{{ addslashes($consignee->Address1) }}', '{{ addslashes($consignee->Address2) }}', '{{ addslashes($consignee->Address3) }}')"
                class="btn-icon btn-icon-success" title="Edit">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>
            <button
                onclick="deactivateConsignee({{ $consignee->ConsigneeID }}, this)"
                class="btn-icon btn-icon-danger" title="Deactivate">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
        No consignees found matching your search.
    </td>
</tr>
@endforelse