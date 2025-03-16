<?php

namespace Modules\Payments\Payments;

use Modules\Payments\Models\Transfer as TransferModel;

class Cash extends Payment
{
    protected string $code = 'cash';
    protected string $name = 'Cash';


    /**
     * Method which return path of blade template which can be displayed in invoice
     */
    public function getMethodTemplate(int $id): array | null
    {
        return [
            'template' => 'payments::cash.default.info',
        ];
    }
}
