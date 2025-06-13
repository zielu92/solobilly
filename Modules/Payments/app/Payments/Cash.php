<?php

namespace Modules\Payments\Payments;

use Modules\Payments\DTO\pdfTemplateData;
use Modules\Payments\Models\Transfer as TransferModel;
use View;

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
