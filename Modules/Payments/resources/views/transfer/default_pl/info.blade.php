<div class="container-xs">
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
                    <p>{{$paymentMethod->data->bankName}}<br>{{$paymentMethod->data->accountNumber}}</p>
                </td>
            </tr>
        </table>
    </div>
</div>
