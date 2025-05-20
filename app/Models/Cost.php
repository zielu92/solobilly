<?php

namespace App\Models;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label(__('costs.name'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('amount')
                ->label(__('costs.amount'))
                ->required()
                ->live()
                ->numeric()
                ->afterStateUpdated(fn($state, callable $set, callable $get) =>
                $set('amount_gross', is_numeric($get('amount')) ? $get('amount')*1.23 : 0)
                ),
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
                ->live()
                ->numeric(),
            Textarea::make('description')
                ->label(__('costs.description'))
                ->columnSpanFull(),
            DatePicker::make('date')
                ->label(__('costs.date'))
                ->required(),
            Select::make('category_id')
                ->label(__('costs.category'))
                ->live()
                ->relationship('category', 'name')
                ->required()
                ->afterStateUpdated(function ($state, $set, $get) {
                    // Store the is_tax_related value in a hidden field for reference
                    if ($state) {
                        $isTaxRelated = \App\Models\CostCategory::find($state)?->is_tax_related ?? true;
                        $set('is_category_tax_related', $isTaxRelated);
                    } else {
                        $set('is_category_tax_related', true);
                    }
                }),
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
->hintAction(
    fn ($state) => $state
        ? Action::make('download_invoice')
            ->label(__('costs.download_invoice'))
            ->action(fn () => Storage::download($state))
        : null
)
                        return null;
                    }
                )
                ->visibility('private')
                ->imageEditor()
                ->visible(fn (callable $get) => !$get('is_category_tax_related'))
                ->default(null),
            FileUpload::make('receipt_file_path')
                ->label(__('costs.file_path'))
                ->directory('costs/receipts')
                ->hintAction(
                    function ($state) {
                        if ($state) {
                            return Action::make('download_receipt')
                                ->label(__('costs.download_receipt'))
                                ->action(fn() => Storage::download($state));
                        }
                        return null;
                    }
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
