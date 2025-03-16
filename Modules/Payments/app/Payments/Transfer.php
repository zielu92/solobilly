<?php

namespace Modules\Payments\Payments;

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
            return route('filament.app.resources.transfers.edit', ['record' => $transferRecord->id]);
        } else {
            return route('filament.app.resources.transfers.create', ['payment_method_id' => $record->id]);
        }
    }

    /**
    * Method which return path of blade template which can be displayed in invoice
    */
    public function getMethodTemplate(int $id): array | null
    {
        $tm = TransferModel::withTrashed()->where('payment_method_id', $id)->first();

        return [
            'template' => 'payments::transfer.default.info',
            'data' => $tm
        ];

    }
}
