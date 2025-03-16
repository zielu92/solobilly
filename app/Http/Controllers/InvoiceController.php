<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Modules\Payments\PaymentMethodsManager;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;


class InvoiceController extends Controller
{
    public function show($id)
    {
        $invoice = Invoice::with('invoiceItems')->findOrFail($id);
        $items = $invoice->invoiceItems;
        $template = 'default';
        $paymentMethod = PaymentMethodsManager::getPaymentMethodTemplate(strtolower($invoice->paymentMethod->method), $invoice->paymentMethod->id);
        return Pdf::view('invoice.template.'.$template.'.pdf', [
            'invoice'       => $invoice,
            'items'         => $items,
            'showQty'       => $items->sum('quantity') !== count($items),
            'showDiscount'  => $items->sum('total_discount') > 0,
            'paymentMethod' => $paymentMethod,
        ])
            ->format('a4')
        ->name("#{$invoice->no}");

    }
}
