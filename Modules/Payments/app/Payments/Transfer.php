<?php

namespace Modules\Payments\Payments;

use Illuminate\Support\Facades\View;
use Modules\Payments\DTO\PdfTemplateData;
use \Modules\Payments\Models\Transfer as TransferModel;


class Transfer extends Payment
{
    protected string $code = 'transfer';
    protected string $name = 'Transfer';
    public bool $editable = true;

    public function registerMethod($id = null)
    {
        return redirect()->route('payments.transfer.create', ['id'=> $id]);
    }

    public function getEditOrCreateURL($record): string | null
    {
        $transferRecord = TransferModel::where('payment_method_id', $record->id)->first();
        if ($transferRecord) {
            return route('filament.admin.resources.transfers.edit', ['record' => $transferRecord->id]);
        } else {
            return route('filament.admin.resources.transfers.create', ['payment_method_id' => $record->id]);
        }
    }

    /**
    * Method which return path of blade template which can be displayed in invoice
    */
    public function getMethodTemplate(int $id, string $template): PdfTemplateData | null
    {
        $tm = TransferModel::withTrashed()->where('payment_method_id', $id)->first();
        $view = 'payments::transfer.' . $template . '.info';
        if (!View::exists($view)) {
            return new PdfTemplateData('payments::transfer.default.info', $tm);
        } else {
            return new PdfTemplateData($view, $tm);
        }
    }
}
