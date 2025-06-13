<?php
$colspan = 5;
if($showQty)
    $colspan += 1;
if($showDiscount)
    $colspan += 1;
?>

    <!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{ public_path('/css/invoice/test/pdf.css') }}" type="text/css">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>
            @if($invoice->type=='proforma')
                Proforma
            @else
                Faktura
            @endif
        </h1>
    </div>

    <div class="invoice-info">
        <p>
            <strong>Faktura nr.:</strong>
            {{$invoice->no}}
        </p>
        <p>
            Oryginał
        </p>
        <p>
            <strong>Miejscowość:</strong>
            {{$invoice->place}}
        </p>
        <p>
            <strong>Data wystawienia:</strong>
            {{$invoice->issue_date}}
        </p>
        <p>
            <strong>Data sprzedaży:</strong>
            {{$invoice->sale_date}}
        </p>
    </div>

    <div class="clear-both"></div>

    <div class="company-details">
        <table class="company-grid">
            <tr>
                <td>
                    <p>
                        <strong>Sprzedawca:</strong><br>
                        {{setting('seller.company_name')}}<br>
                        {{setting('seller.address')}}<br>
                        {{setting('seller.postal_code')}}
                        {{setting('seller.city')}}<br>
                        {{setting('seller.country')}}<br>
                        @if(setting('seller.nip')!='')
                            NIP: {{setting('seller.nip')}}<br>
                        @endif
                    </p>
                </td>
                <td>
                    <p>
                        <strong>Nabywca:</strong><br>
                        {{$invoice->buyer->company_name}}<br>
                        {{$invoice->buyer->address}}<br>
                        {{$invoice->buyer->postal_code}}
                        {{$invoice->buyer->city}}<br>
                        @if($invoice->buyer->nip!='')
                            NIP: {{$invoice->buyer->nip}}<br>
                        @endif
                        @if($invoice->buyer->regon!='')
                            REGON: {{$invoice->buyer->regon}}<br>
                        @endif
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <div class="products-section">
        <table class="products-table">
            <thead>
            <tr>
                <th>
                    <strong>L.p.</strong><br>
                </th>
                <th>
                    <strong>Nazwa Usługi</strong><br>
                </th>
                <th>
                    <strong>Wartość netto</strong><br>
                </th>
                <th class="text-right">
                    <strong>Stawka VAT</strong><br>
                </th>
                @if($showDiscount)
                    <th class="text-right">
                        <strong>Rabat</strong><br>
                    </th>
                @endif
                @if($showQty)
                    <th class="text-right">
                        <strong>Ilość</strong><br>
                    </th>
                @endif
                <th class="text-right">
                    <strong>Kwota VAT</strong><br>
                </th>
                <th class="text-right">
                    <strong>Wartość brutto</strong><br>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td class="text-gray-900">
                        {{$loop->index+1}}
                    </td>
                    <td>
                        {{$item->name}}
                    </td>
                    <td class="text-right">
                        {{$item->price_net}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                    <td class="text-right">
                        {{showTaxRate($item->tax_rate, true)}}
                    </td>
                    @if($showDiscount)
                        <td class="text-right">
                            {{$item->total_discount}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                    @endif
                    @if($showQty)
                        <td class="text-right">
                            {{$item->quantity}}
                        </td>
                    @endif
                    <td class="text-right">
                        {{$item->tax_amount}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                    <td class="text-right">
                        {{$item->price_gross}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endforeach
            @if($invoice->grand_total_gross!=$invoice->grand_total_net)
                <tr class="summary-row">
                    <td colspan="{{$colspan}}" class="text-right">
                        <strong>Wartość netto:</strong>
                    </td>
                    <td class="text-right">
                        {{$invoice->grand_total_net}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endif
            @if($invoice->grand_total_discount!='0.00')
                <tr class="summary-row">
                    <td colspan="{{$colspan}}" class="text-right">
                        <strong>Wartość rabatu:</strong>
                    </td>
                    <td class="text-right">
                        {{$invoice->grand_total_discount}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endif
            @if($invoice->grand_total_tax!='0.00')
                <tr class="summary-row">
                    <td colspan="{{$colspan}}" class="text-right">
                        <strong>Wartość podatku:</strong>
                    </td>
                    <td class="text-right">
                        {{$invoice->grand_total_tax}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endif
            <tr class="total-row">
                <td colspan="{{$colspan}}" class="text-right">
                    <strong>Do Zapłaty</strong>
                </td>
                <td class="text-right">
                    {{$invoice->grand_total_gross}} {{html_entity_decode($invoice->currency->symbol)}}
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="payment-section">
        @if($paymentMethod && isset($paymentMethod->template))
            @include($paymentMethod->template)
        @endif
    </div>

    <div class="comment-section">
        {{$invoice->comment}}
    </div>

    <div class="signature-section">
        <table class="signature-grid">
            <tr>
                <td>
                    Wystawił(a): @if($invoice->issuer_name)({{$invoice->issuer_name}})@endif<br/><br/>
                    <div class="signature-line"></div>
                </td>
                <td>
                    Odebrał(a):<br/><br/>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
