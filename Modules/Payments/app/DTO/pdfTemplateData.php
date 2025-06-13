<?php

namespace Modules\Payments\DTO;

use Spatie\LaravelData\Data;

class pdfTemplateData extends Data
{
    public function __construct(
        public string $template,
        public mixed $data = null,
    ) {
    }
}
