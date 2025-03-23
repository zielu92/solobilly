<?php

namespace Modules\Payments\Models;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["accountNumber", "bankName", "iban", "swift", "beneficiaryName", "beneficiaryAddress", "user_id", "payment_method_id"];

    public static function getForm(): array
    {
        return [
            TextInput::make('accountNumber')
                ->label(__('payments::payments.account_number'))
                ->required()
                ->maxLength(255),
            TextInput::make('bankName')
                ->label(__('payments::payments.bank_name'))
                ->required()
                ->maxLength(255),
            TextInput::make('iban')
                ->label(__('payments::payments.iban'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('swift')
                ->label(__('payments::payments.swift'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('beneficiaryName')
                ->label(__('payments::payments.beneficiary_name'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('beneficiaryAddress')
                ->label(__('payments::payments.beneficiary_address'))
                ->maxLength(255)
                ->default(null),
        ];
    }
}
