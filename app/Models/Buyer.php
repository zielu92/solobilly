<?php

namespace App\Models;

use App\Enum\TypeOfContract;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buyer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'nip',
        'regon',
        'krs',
        'contract_type',
        'contract_rate',
        'color',
        'currency_id',
        'unit_type'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'contract_type' => TypeOfContract::class,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label(__('buyers.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('company_name')
                ->label(__('buyers.company_name'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('email')
                ->label(__('buyers.email'))
                ->email()
                ->maxLength(255)
                ->default(null),
            TextInput::make('phone')
                ->label(__('buyers.phone'))
                ->tel()
                ->maxLength(255)
                ->default(null),
            TextInput::make('address')
                ->label(__('buyers.address'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('city')
                ->label(__('buyers.city'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('postal_code')
                ->label(__('buyers.postal_code'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('country')
                ->label(__('buyers.country'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('nip')
                ->label(__('buyers.tax_id'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('regon')
                ->label(__('buyers.regon'))
                ->maxLength(255)
                ->default(null),
            TextInput::make('krs')
                ->label(__('buyers.krs'))
                ->maxLength(255)
                ->default(null),
            ColorPicker::make('color')
                ->label(__('buyers.color'))
                ->default(randomColorHex()),
            Select::make('contract_type')
                ->required()
                ->label(__('buyers.contract_type'))
                ->options(TypeOfContract::class)
                ->default(TypeOfContract::OTHER)
                ->live(),
            TextInput::make('contract_rate')
                ->label(__('buyers.contract_rate'))
                ->numeric()
                ->default(null),
            TextInput::make('unit_type')
                ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('buyers.unit_type_info'))
                ->label(__('buyers.unit_type'))
                ->visible(fn(Get $get) => $get('contract_type') === 'other' || $get('contract_type') === TypeOfContract::OTHER)
                ->default(null),
            Select::make('currency_id')
                ->label(__('invoices.currency'))
                ->columnSpan(1)
                ->live()
                ->options(
                    Currency::whereIn('id', setting('general.currencies'))->get()->pluck('code', 'id')
                )
                ->default(Currency::find(setting('general.default_currency'))->id)
                ->required()
        ];
    }
}
