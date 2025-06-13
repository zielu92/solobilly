<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Payments\PaymentMethodsManager;
use Illuminate\Support\Facades\View;


class InvoiceController extends Controller
{
    public function show($id)
    {
        $invoice = Invoice::with('invoiceItems')->findOrFail($id);
        $items = $invoice->invoiceItems;

        $template = View::exists("invoice.template.{$invoice->template}.pdf")
            ? $invoice->template
            : 'default';

        $view = "invoice.template.{$template}.pdf";
        $paymentMethod = PaymentMethodsManager::getPaymentMethodTemplate(
            strtolower($invoice->paymentMethod->method),
            $invoice->paymentMethod->id,
            $template
        );

        $pdf = Pdf::loadView($view, [
            'invoice'       => $invoice,
            'items'         => $items,
            'showQty'       => $items->sum('quantity') !== count($items),
            'showDiscount'  => $items->sum('total_discount') > 0,
            'paymentMethod' => $paymentMethod,
        ]);

        return $pdf->download($invoice->no.'.pdf');
    }
}
