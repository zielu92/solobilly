<div class="container-xs">
    <!-- Polish Transfer Payment Section -->
    <div class="payment-details">
        <table class="payment-grid">
            <tr>
                <td class="text-right font-semibold">
                    <p>Sposób zapłaty:</p>
                    <p>Termin zapłaty:</p>
                    <p>Numer rachunku:</p>
                </td>
                <td class="text-left">
                    <p>Transfer</p>
                    <p>{{$invoice->due_date->format('Y-m-d')}}</p>
                    <p>{{$paymentMethod->data?->bankName}}<br>{{$paymentMethod->data?->accountNumber}}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- English Transfer Payment Section -->
    <div class="payment-details">
        <table class="payment-grid">
            <tr>
                <td class="text-right font-semibold">
                    <p>Payment method:</p>
                    <p>Due date:</p>
                    <p>Bank Account (IBAN):</p>
                    <p>Bank Code (SWIFT):</p>
                    <p>Bank Name:</p>
                    <p>Beneficiary Name:</p>
                    <p>Beneficiary Address:</p>
                </td>
                <td class="text-left">
                    <p>Transfer</p>
                    <p>{{$invoice->due_date?->format('Y-m-d')}}</p>
                    <p>{{$paymentMethod->data?->accountNumber}}</p>
                    <p>{{$paymentMethod->data?->swift}}</p>
                    <p>{{$paymentMethod->data?->bankName}}</p>
                    <p>{{$paymentMethod->data?->beneficiaryName}}</p>
                    <p>{{$paymentMethod->data?->beneficiaryAddress}}</p>
                </td>
            </tr>
        </table>
    </div>
</div>
