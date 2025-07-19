<?php
$colspan = 6;
if($showQty)
    $colspan += 1;
if($showDiscount)
    $colspan += 1;
if($invoice->showUnits)
    $colspan +=1;
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
                Invoice
            @endif
        </h1>
    </div>

    <div class="invoice-info">
        <p>
            <strong>Invoice no:</strong>
            {{$invoice->no}}
        </p>
        <p>
            Original
        </p>
        <p>
            <strong>Place:</strong>
            {{$invoice->place}}
        </p>
        <p>
            <strong>Date of issue:</strong>
            {{$invoice->issue_date}}
        </p>
        <p>
            <strong>Date of sell:</strong>
            {{$invoice->sale_date}}
        </p>
    </div>

    <div class="clear-both"></div>

    <div class="company-details">
        <table class="company-grid">
            <tr>
                <td>
                    <p>
                        <strong>Seller:</strong><br>
                        {{setting('seller.company_name')}}<br>
                        {{setting('seller.address')}}<br>
                        {{setting('seller.postal_code')}}
                        {{setting('seller.city')}}<br>
                        {{setting('seller.country')}}<br>
                        @if(setting('seller.nip')!='')
                            Tax ID: {{setting('seller.nip')}}<br>
                        @endif
                    </p>
                </td>
                <td>
                    <p>
                        <strong>Buyer:</strong><br>
                        {{$invoice->buyer->company_name}}<br>
                        {{$invoice->buyer->address}}<br>
                        {{$invoice->buyer->postal_code}}
                        {{$invoice->buyer->city}}<br>
                        @if($invoice->buyer->nip!='')
                            Tax ID: {{$invoice->buyer->nip}}<br>
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
                    <strong>No</strong><br>
                </th>
                <th>
                    <strong>Description</strong><br>
                </th>
                <th>
                    <strong>Unit value</strong><br>
                </th>
                @if($showQty)
                    <th class="text-right">
                        <strong>QTY</strong><br>
                    </th>
                @endif
                @if($invoice->showUnits)
                    <th class="text-right">
                        <strong>Units</strong>
                    </th>
                @endif
                <th class="text-right">
                    <strong>VAT</strong><br>
                </th>
                @if($showDiscount)
                    <th class="text-right">
                        <strong>Discount</strong><br>
                    </th>
                @endif
                <th>
                    <strong>Net value</strong>
                </th>
                <th class="text-right">
                    <strong>Vat amount</strong><br>
                </th>
                <th class="text-right">
                    <strong>Total amount</strong><br>
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
                    @if($showQty)
                        <td class="text-right">
                            {{$item->quantity}}
                        </td>
                    @endif
                    @if($invoice->showUnits)
                        <td class="text-right">
                            {{$item->units}}
                        </td>
                    @endif
                    <td class="text-right">
                        {{showTaxRate($item->tax_rate, true)}}
                    </td>
                    @if($showDiscount)
                        <td class="text-right">
                            {{$item->total_discount}} {{html_entity_decode($invoice->currency->symbol)}}
                        </td>
                    @endif
                    <td class="text-right">
                        {{$item->total_net}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                    <td class="text-right">
                        {{$item->total_tax}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                    <td class="text-right">
                        {{$item->total_gross}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endforeach
            @if($invoice->grand_total_gross!=$invoice->grand_total_net)
                <tr class="summary-row">
                    <td colspan="{{$colspan}}" class="text-right">
                        <strong>Sub-Total</strong>:
                    </td>
                    <td class="text-right">
                        {{$invoice->grand_total_net}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endif
            @if($invoice->grand_total_discount!='0.00')
                <tr class="summary-row">
                    <td colspan="{{$colspan}}" class="text-right">
                        <strong>Discount</strong>:
                    </td>
                    <td class="text-right">
                        {{$invoice->grand_total_discount}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endif
            @if($invoice->grand_total_tax!='0.00')
                <tr class="summary-row">
                    <td colspan="{{$colspan}}" class="text-right">
                        <strong>Tax</strong>:
                    </td>
                    <td class="text-right">
                        {{$invoice->grand_total_tax}} {{html_entity_decode($invoice->currency->symbol)}}
                    </td>
                </tr>
            @endif
            <tr class="total-row">
                <td colspan="{{$colspan}}" class="text-right">
                    <strong>Total to pay</strong>
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
                    Issued by: @if($invoice->issuer_name)({{$invoice->issuer_name}})@endif<br/><br/>
                    <div class="signature-line"></div>
                </td>
                <td>
                    Received by:<br/><br/>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
