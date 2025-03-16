<?php

namespace Modules\Payments;

use Illuminate\Support\Facades\Config;

class PaymentMethodsManager
{
    /**
     * Returns all supported payment methods
     *
     * @return array
     */
    public static function getPaymentMethods(): array
    {
        return collect(Config::get('payment_methods'))
            ->map(function ($paymentMethod) {
                $object = app($paymentMethod['class']);
                if ($object->isAvailable()) {
                    return [
                        'method' => $object->getCode(),
                        'method_title' => $object->getTitle(),
                        'description' => $object->getDescription(),
                        'sort' => $object->getSortOrder(),
                    ];
                }
                return null;
            })
            ->filter()
            ->sortBy('sort')
            ->values()
            ->all();
    }

    /** Execute additional features (like auth, to external service or anything like this)
     * @param string $method
     * @param int|null $id
     * @return object
     */
    public static function registerMethod(string $method, int $id = null): object
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->registerMethod($id);
    }


    public static function canEdit(string $method): bool
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->editable;
    }

    public static function getEditCreateRoute(string $method, $record)
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getEditOrCreateURL($record);
    }

    /**
     * Get title of the payment methods
     * @parm string $method
     * @return object
     */
    public static function getTitle(string $method)
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getTitle();
    }

    /**
     * get the URL - if there is any
     * @param string $method
     * @param int $id
     * @return mixed
     */
    public static function getPaymentURL(string $method, int $id): object
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getChannelURL($id);
    }

    /**
     * check if payment method have url
     * @param string $method
     * @return object
     */
    public static function haveURL(string $method): object
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->haveURL();
    }

    public static function getPaymentMethodTemplate(string $method, int $id): array | null
    {
        $object = app(config('payment_methods.' . $method . '.class'));
        return $object->getMethodTemplate($id);
    }

}
