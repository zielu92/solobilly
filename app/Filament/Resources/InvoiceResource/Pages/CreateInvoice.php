<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Models\Buyer;
use App\Models\InvoiceBuyer;
use Filament\Actions;
use App\Models\InvoiceItem;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function beforeFill(): void
    {
        // Runs before the form fields are populated with their default values.
    }

    protected function afterCreate(): void
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
        $buyer = Buyer::find($invoice->buyer_id);
        $buyer->invoice_id = $invoice->id;
        InvoiceBuyer::create($buyer->toArray());
    }
}
