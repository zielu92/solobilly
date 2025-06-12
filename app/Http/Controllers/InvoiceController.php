<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Payments\PaymentMethodsManager;


class InvoiceController extends Controller
{
    public function show($id)
    {
        $invoice = Invoice::with('invoiceItems')->findOrFail($id);
        $items = $invoice->invoiceItems;
        $template = 'default';
        $paymentMethod = PaymentMethodsManager::getPaymentMethodTemplate(strtolower($invoice->paymentMethod->method), $invoice->paymentMethod->id);

        $pdf = Pdf::loadView('invoice.template.test.pdf',  [
            'invoice'       => $invoice,
            'items'         => $items,
            'showQty'       => $items->sum('quantity') !== count($items),
            'showDiscount'  => $items->sum('total_discount') > 0,
            'paymentMethod' => $paymentMethod,
        ]);

        return $pdf->stream($invoice->no.'.pdf');
    }
}
