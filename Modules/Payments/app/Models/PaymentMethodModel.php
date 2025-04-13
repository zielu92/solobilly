<?php

namespace Modules\Payments\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Payments\PaymentMethodsManager;

class PaymentMethodModel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["user_id", "name", "description", "url", "method", "active"];
    protected $table = "payment_methods";

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label(__('payments::payments.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('description')
                ->label(__('payments::payments.description'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('url')
                ->label(__('payments::payments.url'))
                ->maxLength(255)
                ->default(null),
            Select::make('method')
                ->required()
                ->options(
                    collect(PaymentMethodsManager::getPaymentMethods())->mapWithKeys(function ($method) {
                        return [$method['method'] => __('payments::payments.methods.'.strtolower($method['method_title']))];
                    })
                )
                ->default(null),
            Toggle::make('active')
                ->label(__('payments::payments.active'))
                ->default(true),
        ];
    }
}
