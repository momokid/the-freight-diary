@if($containers->isEmpty())
    <div id="empty-staging" style="padding: 1.5rem; text-align: center; color: var(--text-muted); font-size: 0.875rem; border: 1.5px dashed var(--border-color); border-radius: 8px;">
        No containers added yet. Add at least one container before submitting.
    </div>
@else
    <table class="data-table">
        <thead>
            <tr>
                <th>Seal No</th>
                <th>Container No</th>
                <th style="width: 80px;">Size</th>
                <th style="width: 100px;">Weight (KG)</th>
                <th style="width: 120px;">Handling Cost</th>
                <th style="width: 60px; text-align: center;">Remove</th>
            </tr>
        </thead>
        <tbody>
            @foreach($containers as $container)
            <tr>
                <td class="td-mono">{{ $container->SealNo }}</td>
                <td class="td-mono">{{ $container->ContainerNo }}</td>
                <td class="td-muted">{{ $container->ContainerSize }}</td>
                <td class="td-muted">{{ number_format($container->Weight, 2) }}</td>
                <td style="color: #16a34a; font-weight: 500;">{{ number_format($container->HandlingCost, 2) }}</td>
                <td style="text-align: center;">
                    <button onclick="removeContainer('{{ $container->BOL }}', '{{ $container->ContainerNo }}')"
                        class="btn-icon btn-icon-danger" title="Remove">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif