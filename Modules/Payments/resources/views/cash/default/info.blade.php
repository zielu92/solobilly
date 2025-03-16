<div class="container mx-auto  text-xs">
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div class="text-right font-semibold">
            <p class="mb-2">Sposób zapłaty:</p>
            <p class="mb-2">Termin zapłaty:</p>
            <p class="mb-2"></p>
        </div>
        <div class="text-left">
            <p class="mb-2">Gotówka</p>
            <p class="mb-2">{{$invoice->due_date->format('Y-m-d')}}</p>
            <p class="mb-2">@if($invoice->payment_status=="paid") Zapłacono @endif</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-8">
        <div class="text-right font-semibold">
            <p class="mb-2">Payment method:</p>
            <p class="mb-2">Due date:</p>
            <p class="mb-2"></p>

        </div>
        <div class="text-left">
            <p class="mb-2">Cash</p>
            <p class="mb-2">{{$invoice->due_date->format('Y-m-d')}}</p>
            <p class="mb-2">@if($invoice->payment_status=="paid") Paid @endif</p>
        </div>
    </div>
</div>
