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
                ->required()
                ->maxLength(255),
            TextInput::make('description')
                ->maxLength(255)
                ->default(null),
            TextInput::make('url')
                ->maxLength(255)
                ->default(null),
            Select::make('method')
                ->options(collect(PaymentMethodsManager::getPaymentMethods())->pluck('method', 'method_title'))
                ->default(null),
            Toggle::make('active')
                ->default(true),
        ];
    }
}
