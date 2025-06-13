<?php

namespace Modules\Payments\Payments;

use Modules\Payments\DTO\PdfTemplateData;
use Modules\Payments\Models\Transfer as TransferModel;
use Illuminate\Support\Facades\View;

class Cash extends Payment
{
    protected string $code = 'cash';
    protected string $name = 'Cash';


    /**
     * Method which return path of blade template which can be displayed in invoice
     */
    public function getMethodTemplate(int $id, string $template): PdfTemplateData | null
    {
        $view = 'payments::cash.' . $template . '.info';
        if (!View::exists($view)) {
            return new PdfTemplateData('payments::cash.default.info');
        } else {
            return new PdfTemplateData($view);
        }
    }
}
