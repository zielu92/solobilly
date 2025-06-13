<?php

namespace Modules\Payments\Payments;

use Illuminate\Support\Facades\Config;
use Modules\Payments\DTO\PdfTemplateData;

abstract class Payment
{
    protected bool $haveURL = false;
    protected string $code;

    public bool $editable = false;

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->getConfigValue('active') ?? false;
    }

    /**
     * get original id of method model
     * @param int $id
     * @return int|null
     */
    public function getModelId(int $id): int|null
    {
        return null;
    }

    /**
     * Retrieve information from payment methods config
     *
     * @param  string $field
     * @return mixed
     */
    public function getConfigValue(string $field): mixed {
        $code = $this->getCode();
        return Config::get('payment_methods.' . $code . '.' . $field);
    }

    /**
     * Get the code of the instance
     * @return string
     */
    public function getCode(): string
    {
        if(empty($this->code)) {
            // throw exception
        }
        return $this->code;
    }

    /**
     * Return payment method description
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getConfigValue('title');
    }

    /**
     * Return payment method description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getConfigValue('description');
    }

    /**
     * Return payment method sort order
     *
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->getConfigValue('sort');
    }


    /**
     * return link for payment
     * @param int $id
     * @return string
     */
    public function getPaymentURL(int $id): string
    {
        return 'not available';
    }

    /**
     * If store have URL link
     * @return mixed
     */
    public function haveURL(): mixed
    {
        return $this->haveURL;
    }

    /**
     * Get the path for edit or create resource
     */
    public function getEditOrCreateURL($record): string | null
    {
        return null;
    }

    /**
     * Method which return path of blade template which can be displayed in invoice
     */
    abstract public function getMethodTemplate(int $id, string $template): PdfTemplateData | null;
}
