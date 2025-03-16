<div class="container mx-auto  text-xs">
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div class="text-right font-semibold">
            <p class="mb-2">Sposób zapłaty:</p>
            <p class="mb-2">Termin zapłaty:</p>
            <p class="mb-2">Numer rachunku:</p>
        </div>
        <div class="text-left">
            <p class="mb-2">Transfer</p>
            <p class="mb-2">{{$invoice->due_date->format('Y-m-d')}}</p>
            <p class="mb-2">{{$paymentMethod['data']->bankName}}<br>{{$paymentMethod['data']->accountNumber}}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-8">
        <div class="text-right font-semibold">
            <p class="mb-2">Payment method:</p>
            <p class="mb-2">Due date:</p>
            <p class="mb-2">Bank Account (IBAN):</p>
            <p class="mb-2">Bank Code (SWIFT):</p>
            <p class="mb-2">Bank Name:</p>
            <p class="mb-2">Beneficiary Name:</p>
            <p class="mb-2">Beneficiary Address:</p>
        </div>
        <div class="text-left">
            <p class="mb-2">Transfer</p>
            <p class="mb-2">{{$invoice->due_date->format('Y-m-d')}}</p>
            <p class="mb-2">{{$paymentMethod['data']->accountNumber}}</p>
            <p class="mb-2">{{$paymentMethod['data']->swift}}</p>
            <p class="mb-2">{{$paymentMethod['data']->bankName}}</p>
            <p class="mb-2">{{$paymentMethod['data']->beneficiaryName}}</p>
            <p class="mb-2">{{$paymentMethod['data']->beneficiaryAddress}}</p>
        </div>
    </div>

</div>
