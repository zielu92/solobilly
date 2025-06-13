<div class="container">
    <div class="payment-details">
        <table class="payment-grid">
            <tr>
                <td class="text-right font-semibold">
                    <p>Sposób zapłaty:</p>
                    <p>Termin zapłaty:</p>
                    <p></p>
                </td>
                <td class="text-left">
                    <p>Gotówka</p>
                    <p>{{$invoice->due_date->format('Y-m-d')}}</p>
                    <p>@if($invoice->payment_status=="paid") Zapłacono @endif</p>
                </td>
            </tr>
        </table>
    </div>
</div>
