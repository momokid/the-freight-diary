@php($bank = \App\Services\BankDetailsService::active())

@if ($bank)
    <div class="bank-details">
        <div class="title">Bank Account Details</div>
        <div><strong>Bank Account:</strong> {{ $bank->AccountName }}</div>
        <div><strong>Account#:</strong> {{ $bank->AccountNo }}</div>
        <div><strong>Bank Name:</strong> {{ $bank->BankName }}</div>
        <div><strong>Branch:</strong> {{ $bank->Branch }}</div>
    </div>
@endif
