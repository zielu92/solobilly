<div class="container">
    <div class="payment-details">
        <table class="payment-grid">
            <tr>
                <td class="text-right font-semibold">
                    <p>Payment method:</p>
                    <p>Due date:</p>
                    <p></p>
                </td>
                <td class="text-left">
                    <p>Cash</p>
                    <p>{{$invoice->due_date->format('Y-m-d')}}</p>
                    <p>@if($invoice->payment_status=="paid") Paid @endif</p>
                </td>
            </tr>
        </table>
    </div>
</div>
