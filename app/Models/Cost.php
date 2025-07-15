<?php

namespace App\Models;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Cost extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'amount',
        'currency_id',
        'amount_gross',
        'description',
        'date',
        'category_id',
        'invoice_number',
        'invoice_file_path',
        'receipt_file_path',
        'invoice_date',
        'invoice_due_date',
        'payment_date',
        'user_id',
        'percent_deductible_from_taxes'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'amount' => 'decimal:2',
        'amount_gross' => 'decimal:2',
        'date' => 'date',
        'category_id' => 'integer',
        'invoice_date' => 'date',
        'invoice_due_date' => 'date',
        'payment_date' => 'date',
        'user_id' => 'integer',
        'percent_deductible_from_taxes' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CostCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function afterTaxDeductibleCostNet(): Attribute
    {
        return Attribute::get(
            fn () => round($this->amount * ($this->percent_deductible_from_taxes / 100), 2)
        );
    }

    public function afterTaxDeductibleCostGross(): Attribute
    {
        return Attribute::get(
            fn () => round($this->amount_gross * ($this->percent_deductible_from_taxes / 100), 2)
        );
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label(__('costs.name'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull()
                ->columnSpan(1),
            Select::make('category_id')
                ->label(__('costs.category'))
                ->live()
                ->relationship('category', 'name')
                ->required()
                ->afterStateHydrated(function ($state, $set, $get) {
                    // Initialize on edit - check the selected category when form loads
                    if ($state) {
                        $isTaxRelated = \App\Models\CostCategory::find($state)?->is_tax_related ?? true;
                        $set('is_category_tax_related', $isTaxRelated);
                    } else {
                        $set('is_category_tax_related', true);
                    }
                })
                ->afterStateUpdated(function ($state, $set, $get) {
                    // Store the is_tax_related value in a hidden field for reference
                    if ($state) {
                        $isTaxRelated = \App\Models\CostCategory::find($state)?->is_tax_related ?? true;
                        $set('is_category_tax_related', $isTaxRelated);
                        $set('percent_deductible_from_taxes', 0);
                    } else {
                        $set('is_category_tax_related', true);
                    }
                }),
            TextInput::make('amount')
                ->label(__('costs.amount'))
                ->required()
                ->live()
                ->numeric()
                ->minValue(0)
                ->afterStateUpdated(function($state, callable $set, callable $get) {
                    if(setting('invoice.default_tax_rate')!= null) {
                        $set('amount_gross', round(is_numeric($get('amount')) ? $get('amount') * setting('invoice.default_tax_rate') : 0, 2));
                    }
                }),
            Select::make('currency_id')
                ->label(__('invoices.currency'))
                ->columnSpan(1)
                ->live()
                ->options(
                    Currency::whereIn('id', (array) setting('general.currencies', []))
                        ->pluck('code', 'id')
                )
                ->default(Currency::find(setting('general.default_currency'))->id)
                ->required(),
            TextInput::make('amount_gross')
                ->label(__('costs.amount_gross'))
                ->required()
                ->minValue(0)
                ->live()
                ->numeric(),
            TextInput::make('percent_deductible_from_taxes')
                ->label(__('costs.percent_deductible_from_taxes'))
                ->default(100)
                ->required()
                ->maxValue(100)
                ->minValue(0)
                ->suffix("%")
                ->numeric(),
            Textarea::make('description')
                ->label(__('costs.description'))
                ->columnSpanFull(),
            DatePicker::make('date')
                ->label(__('costs.date'))
                ->required(),
            Hidden::make('is_category_tax_related')
                ->default(true),
            TextInput::make('invoice_number')
                ->label(__('costs.invoice_number'))
                ->maxLength(255)
                ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                ->default(null),
            DatePicker::make('invoice_date')
                ->label(__('costs.invoice_date'))
                ->visible(fn (callable $get) => !$get('is_category_tax_related')),
            FileUpload::make('invoice_file_path')
                ->label(__('costs.invoice_file'))
                ->directory('costs/invoices')
                ->previewable()
                ->hintAction(
                    fn ($state) => $state
                        ? Action::make('download_invoice')
                            ->label(__('costs.download_invoice'))
                            ->action(function () use ($state) {
                                $filePath = is_array($state) ? reset($state) : $state;
                                return Storage::download($filePath);
                            })
                        : null
                )
                ->visibility('private')
                ->imageEditor()
                ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                ->default(null),
            FileUpload::make('receipt_file_path')
                ->label(__('costs.file_path'))
                ->directory('costs/receipts')
                ->hintAction(
                    fn ($state) => $state
                        ? Action::make('download_receipt')
                            ->label(__('costs.download_receipt'))
                            ->action(function () use ($state) {
                                $filePath = is_array($state) ? reset($state) : $state;
                                return Storage::download($filePath);
                            })
                        : null
                )
                ->visibility('private')
                ->imageEditor()
                ->default(null),
            DatePicker::make('invoice_due_date')
                ->label(__('costs.invoice_due'))
                ->visible(fn (callable $get) => !$get('is_category_tax_related')),
            DatePicker::make('payment_date')
                ->label(__('costs.payment_date')),
        ];
    }
}
