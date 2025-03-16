<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Models\Buyer;
use App\Models\InvoiceBuyer;
use Filament\Actions;
use App\Models\Invoice;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\InvoiceResource;
use App\Models\InvoiceItem;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function afterSave(): void
    {
        $invoice = $this->record;
        $totals = [
            'grand_total_net' => 0,
            'grand_total_tax' => 0,
            'grand_total_gross' => 0,
            'grand_total_discount' => 0,
        ];

        foreach ($invoice->invoiceItems as $item) {
            // Accumulate totals
            $totals['grand_total_net'] += $item['total_net'];
            $totals['grand_total_tax'] += $item['total_tax'];
            $totals['grand_total_gross'] += $item['total_gross'];
            $totals['grand_total_discount'] += $item['total_discount'];
        }

        $invoice->update($totals);

        //copy buyer data to invoice
        $invoice->invoiceBuyer->delete();
        $buyer = Buyer::find($invoice->buyer_id);
        $buyer->invoice_id = $invoice->id;
        InvoiceBuyer::create($buyer->toArray());
    }
}
