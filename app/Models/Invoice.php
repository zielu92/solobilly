<?php

namespace App\Models;

use App\Enum\TypeOfContract;
use Carbon\Carbon;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Log;
use Modules\Payments\Models\PaymentMethodModel;
use phpDocumentor\Reflection\Types\Boolean;
use PHPStan\PhpDocParser\Ast\Attribute;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no',
        'buyer_id',
        'type',
        'payment_status',
        'place',
        'sale_date',
        'due_date',
        'issue_date',
        'parent_id',
        'user_id',
        'comment',
        'issuer_name',
        'grand_total_net',
        'grand_total_gross',
        'grand_total_tax',
        'grand_total_discount',
        'paid',
        'due',
        'path',
        'currency_id',
        'payment_method_id',
        'template',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'buyer_id' => 'integer',
        'due_date' => 'date',
        'parent_id' => 'integer',
        'user_id' => 'integer',
        'total_net' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'paid' => 'decimal:2',
        'due' => 'decimal:2',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function invoiceBuyer(): HasOne
    {
        return $this->hasOne(InvoiceBuyer::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getShowUnitsAttribute(): bool
    {
        return $this->invoiceItems()->where('display_units', 1)->exists();
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethodModel::class);
    }

    public static function getForm(int $buyerId = null): array
    {
        return [
            TextInput::make('no')
                ->label(__('invoices.invoice_no'))
                ->autocomplete(false)
                ->columnSpan(2)
                ->required()
                ->default(function ()  {
                    $defaultNumberFormat = setting('invoice.default_pattern');
                    if($defaultNumberFormat) {
                        $now = Carbon::now();
                        $month = $now->format('n');
                        $year = $now->format('Y');

                        $previousThisMonth = Invoice::query()
                                ->whereMonth('issue_date', $month)
                                ->whereYear('issue_date', $year)
                                ->count() + 1;
                        $previousThisYear = Invoice::query()
                                ->whereYear('issue_date', $year)
                                ->count() + 1;

                        $random = Str::random(5);

                        $replacements = [
                            '{nm}' => $previousThisMonth,
                            '{ny}' => $previousThisYear,
                            '{m}' => $month,
                            '{y}' => $year,
                            '{random}' => $random,
                        ];
                        return str_replace(array_keys($replacements), array_values($replacements), $defaultNumberFormat);
                    }
                    return Str::random(5);
                }),

            Select::make('type')
                ->label(__('invoices.type'))
                ->columnSpan(1)
                ->options([
                    'regular' => __('invoices.types.regular'),
                    'proforma' => __('invoices.types.proforma'),
                    'draft' => __('invoices.types.draft'),
                    'cancelled' => __('invoices.types.cancelled'),
                ])
                ->default('regular')
                ->required(),

            Select::make('payment_status')
                ->label(__('invoices.payment_status'))
                ->columnSpan(2)
                ->options([
                    'paid' => __('invoices.paid'),
                    'not_paid' => __('invoices.notpaid'),
                ])
                ->default('not_paid')
                ->required(),

            Select::make('buyer_id')
                ->label(__('invoices.buyer'))
                ->hidden(function() use ($buyerId) {
                    return $buyerId !== null;
                })
                ->relationship('buyer', 'name')
                ->columnSpan(2)
                ->preload()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    $buyer = Buyer::select('currency_id', 'unit_type')->with('currency')->find($state);
                    if ($buyer && $buyer->currency_id) {
                        $set('currency_id', $buyer->currency_id);
                        $set('currency_code', $buyer->currency->code);
                        $set('items.0.display_units', $buyer->unit_type!=null);
                        $set('items.0.units', $buyer->unit_type);
                        $set('items.0.name', "");
                        $set('items.0.discount', 0);
                    }
                })
                ->hintAction(
                    function($state) {
                        return self::getModal($state);
                    }
                )
                ->required(),

            Select::make('currency_id')
                ->label(__('invoices.currency'))
                ->columnSpan(1)
                ->live()
                ->options(
                    Currency::whereIn('id', setting('general.currencies'))->get()->pluck('code', 'id')
                )
                ->default(Currency::find(setting('general.default_currency'))->id)
                ->afterStateUpdated(function ($state, callable $set) use (&$currencySymbol) {
                    $currency = Currency::find($state);
                    $currencySymbol = $currency ? $currency->code : '';
                    $set('currency_code', $currencySymbol);
                })
                ->required(),

            Hidden::make('currency_code')
                ->default(function () {
                    $defaultCurrencyId = Currency::find(setting('general.default_currency'))->id;
                    $currency = Currency::find($defaultCurrencyId);
                    return $currency ? $currency->code : '';
                }),

            Select::make('payment_method_id')
                ->label(__('invoices.payment_method'))
                ->relationship('paymentMethod', 'name')
                ->columnSpan(2)
                ->required(),

            TextInput::make('place')
                ->label(__('invoices.place_of_issue'))
                ->columnSpan(2)
                ->default(setting('invoice.default_place'))
                ->nullable(),

            DatePicker::make('sale_date')
                ->label(__('invoices.sale_date'))
                ->default(now())
                ->columnSpan(2)
                ->nullable(),

            DatePicker::make('issue_date')
                ->label(__('invoices.issue_date'))
                ->default(now())
                ->columnSpan(2)
                ->required(),

            DatePicker::make('due_date')
                ->label(__('invoices.due_date'))
                ->default(now()->addDays(14))
                ->columnSpan(2)
                ->required(),

            TextInput::make('comment')
                ->label(__('invoices.comments'))
                ->columnSpan(2)
                ->nullable(),

            Select::make('template')
                ->label(__('invoices.template'))
                ->columnSpan(2)
                ->default('default')
                ->options([
                   'default' => __('invoices.templates.default'),
                   'default_pl' => __('invoices.templates.default_pl'),
                   'default_en' => __('invoices.templates.default_en'),
                ]),

            TextInput::make('issuer_name')
                ->label(__('invoices.issuer_name'))
                ->default(setting('invoice.default_issuer'))
                ->columnSpan(2)
                ->nullable(),

            Repeater::make('items')
                ->label(__('invoices.invoice_items'))
                ->columnSpanFull()
                ->defaultItems(0)
                ->reorderableWithButtons()
                ->columns(12)
                ->schema([
                    Textarea::make('name')
                        ->label(__('invoices.item_name'))
                        ->columnSpan(3)
                        ->required(),

                    TextInput::make('quantity')
                        ->label(__('invoices.quantity'))
                        ->columnSpan(2)
                        ->lazy()
                        ->debounce()
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    Toggle::make('display_units')
                        ->label(__('invoices.display_units'))
                        ->inline(false)
                        ->columnSpan(2)
                        ->live(),

                    TextInput::make('units')
                        ->label(__('invoices.units'))
                        ->columnSpan(2)
                        ->visible(fn(Get $get) => $get('display_units'))
                        ->required(),

                    TextInput::make('price_net')
                        ->label(__('invoices.net_price'))
                        ->numeric()
                        ->lazy()
                        ->debounce()
                        ->columnSpan(3)
                        ->minValue(0.01)
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->required()
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    Select::make('tax_rate')
                        ->label(__('invoices.tax_rate'))
                        ->columnSpan(3)
                        ->lazy()
                        ->debounce()
                        ->default(setting('invoice.default_tax_rate'))
                        ->options([
                            '23' => '23%',
                            '22' => '22%',
                            '8' => '8%',
                            '5' => '5%',
                            '0' => '0%',
                            'zw' => __('invoices.tax_rates.zw'),
                            'np' => __('invoices.tax_rates.np'),
                        ])
                        ->required()
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    TextInput::make('discount')
                        ->label(__('invoices.discount'))
                        ->lazy()
                        ->debounce()
                        ->columnSpan(3)
                        ->nullable()
                        ->numeric()
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotals($set, $get)),

                    TextInput::make('price_gross')
                        ->label(__('invoices.gross_price'))
                        ->numeric()
                        ->columnSpan(3)
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->readOnly(),

                    TextInput::make('tax_amount')
                        ->label(__('invoices.tax_amount'))
                        ->columnSpan(3)
                        ->readOnly()
                            ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->numeric(),

                    TextInput::make('total_net')
                        ->label(__('invoices.total_net'))
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->numeric(),

                    TextInput::make('total_gross')
                        ->label(__('invoices.total_gross'))
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->numeric(),

                    TextInput::make('total_tax')
                        ->label(__('invoices.total_tax'))
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->numeric(),

                    TextInput::make('total_discount')
                        ->label(__('invoices.total_discount'))
                        ->columnSpan(3)
                        ->readOnly()
                        ->suffix(fn(Get $get) => $get('../../currency_code'))
                        ->numeric(),
                ])
                ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateGrandTotals($set, $get))
                ->afterStateHydrated(fn(callable $set, callable $get) => self::updateGrandTotals($set, $get))
                ->cloneable()
                ->relationship('InvoiceItems')
                ->required(),
                Section::make(__('invoices.grand_total_summary'))
                    ->columns(12)
                    ->schema([
                        Placeholder::make('grand_total_net')
                            ->label(__('invoices.grand_total_net'))
                            ->content(fn(Get $get) => number_format($get('grand_total_net') ?? 0, 2) . ' '.$get('currency_code'))
                            ->columnSpan(3),

                        Placeholder::make('grand_total_tax')
                            ->label(__('invoices.grand_total_tax'))
                            ->content(fn(Get $get) => number_format($get('grand_total_tax') ?? 0, 2) . ' '.$get('currency_code'))
                            ->columnSpan(3),

                        Placeholder::make('grand_total_gross')
                            ->label(__('invoices.grand_total_gross'))
                            ->content(fn(Get $get) => number_format($get('grand_total_gross') ?? 0, 2) . ' '.$get('currency_code'))
                            ->columnSpan(3),

                        Placeholder::make('grand_total_discount')
                            ->label(__('invoices.grand_total_discount'))
                            ->content(fn(Get $get) => number_format($get('grand_total_discount') ?? 0, 2) . ' '.$get('currency_code'))
                            ->columnSpan(3),
                    ]),
            ];
    }

    private static function updateWorklogCalculations($startDate, $endDate, $contractType, callable $set): void
    {
        if (!$startDate || !$endDate) {
            $set('worklog_amount', '0.00');
        }

        if ($contractType === TypeOfContract::DAILY) {
            $days = WorkLog::calculateWorkingDaysBetweenDates($startDate, $endDate);
            $set('worklog_amount', $days . ' working days');
        } else {
            $hours = WorkLog::calculateTotalHoursBetweenDates($startDate, $endDate);
            $set('worklog_amount', $hours . ' hours');
        }
    }

    public static function updateTotals(callable $set, callable $get): array
    {
        $quantityValue = $get('quantity');
        $priceNetValue = $get('price_net');
        $discountValue = $get('discount');
        $taxRateValue = $get('tax_rate');

        // Ensure values exist and are numeric with proper defaults
        $quantity = (is_null($quantityValue) || !is_numeric($quantityValue)) ? 0 : (int) $quantityValue;
        $priceNet = (is_null($priceNetValue) || !is_numeric($priceNetValue)) ? 0.00 : (float) $priceNetValue;
        $discount = (is_null($discountValue) || !is_numeric($discountValue)) ? 0.00 : (float) $discountValue;
        $taxRate = $taxRateValue ?? '23';

        // Determine the tax rate
        $taxPercentage = in_array($taxRate, ['zw', 'np']) ? 0 : (int) $taxRate;

        // Calculate values
        $totalNet = max(($quantity * $priceNet) - $discount, 0);
        $totalTax = round(($totalNet * $taxPercentage) / 100, 2);
        $priceGross = round(($priceNet - $discount) + (($priceNet * $taxPercentage) / 100), 2);
        $taxAmount = round((($priceNet - $discount) * $taxPercentage) / 100, 2);
        $totalGross = round($totalNet + $totalTax, 2);
        $discount = round($discount, 2);

        // Create a mapping of keys to values
        $fields = [
            'price_gross'    => $priceGross,
            'tax_amount'     => $taxAmount,
            'total_net'      => $totalNet,
            'total_tax'      => $totalTax,
            'total_gross'    => $totalGross,
            'total_discount' => $discount,
        ];

        // Iterate and set values dynamically
        foreach ($fields as $key => $value) {
            $set($key, $value);
        }
        // Call grand totals update
        self::updateGrandTotals($set, $get);

        return $fields;
    }


    public static function updateGrandTotals(callable $set, callable $get): void
    {
        $items = $get('items') ?? [];

        $totalNet = 0.00;
        $totalTax = 0.00;
        $totalGross = 0.00;
        $totalDiscount = 0.00;

        foreach ($items as $index => $item) {
            // Safely get values from the item array with null checks
            $quantity = isset($item['quantity']) && is_numeric($item['quantity']) ? (int) $item['quantity'] : 0;
            $priceNet = isset($item['price_net']) && is_numeric($item['price_net']) ? (float) $item['price_net'] : 0.00;
            $discount = isset($item['discount']) && is_numeric($item['discount']) ? (float) $item['discount'] : 0.00;
            $taxRate = $item['tax_rate'] ?? '23';

            // Skip calculation if essential values are missing or zero
            if ($quantity <= 0 || $priceNet <= 0) {
                continue;
            }

            // Calculate item totals
            $itemTotalNet = max(($quantity * $priceNet) - $discount, 0);
            $taxPercentage = in_array($taxRate, ['zw', 'np']) ? 0 : (int) $taxRate;
            $itemTotalTax = round(($itemTotalNet * $taxPercentage) / 100, 2);
            $itemTotalGross = round($itemTotalNet + $itemTotalTax, 2);

            // Sum up the values
            $totalNet += $itemTotalNet;
            $totalTax += $itemTotalTax;
            $totalGross += $itemTotalGross;
            $totalDiscount += $discount;
        }

        // Update the grand total fields
        $set('grand_total_net', round($totalNet, 2));
        $set('grand_total_tax', round($totalTax, 2));
        $set('grand_total_gross', round($totalGross, 2));
        $set('grand_total_discount', round($totalDiscount, 2));
    }

    public static  function getModal($state): Action|null
    {
        if ($state) {
            $buyer = Buyer::select('contract_type', 'contract_rate', 'currency_id', 'name', 'unit_type')->with('currency')->find($state);
            if ($buyer->contract_type === TypeOfContract::DAILY || $buyer->contract_type === TypeOfContract::HOURLY || $buyer->contract_type === TypeOfContract::OTHER) {
                //load the values for specific buyer
                $displayRates = $buyer->contract_type !== TypeOfContract::OTHER || $buyer->unit_type!=null;
                $description = sprintf('%s %s %s %s/%s', __('invoices.default_rates'), $buyer->name, number_format($buyer->contract_rate, 2, ','), $buyer->currency->code, $buyer->contract_type->getLabel());

                return Action::make('worklogs')
                    ->modalHeading(__('invoices.worklogs'))
                    ->label(__('invoices.worklogs_label'))
                    ->modalDescription($displayRates ? $description : "")
                    ->form([
                        Section::make()
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date')
                                    ->label(__('invoices.start_period'))
                                    ->default(Carbon::now()->startOfMonth())
                                    ->columnSpan(1)
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        self::updateWorklogCalculations($state, $get('end_date'), $get('contract_type'), $set);
                                    }),

                                DatePicker::make('end_date')
                                    ->label(__('invoices.end_period'))
                                    ->default(now())
                                    ->columnSpan(1)
                                    ->nullable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        self::updateWorklogCalculations($get('start_date'), $state, $get('contract_type'), $set);
                                    }),
                            ]),
                        Section::make(__('invoices.calculations'))
                            ->columns(7)
                            ->schema([
                                Placeholder::make('worklog_amount')
                                    ->hiddenLabel()
                                    ->content(function (callable $get) use ($buyer) {
                                        $startDate = $get('start_date');
                                        $endDate = $get('end_date');

                                        if (!$startDate || !$endDate) {
                                            return '0';
                                        }
                                        if ($buyer->contract_type === TypeOfContract::DAILY) {
                                            return WorkLog::calculateWorkingDaysBetweenDates($startDate, $endDate);
                                        } else if ($buyer->contract_type === TypeOfContract::OTHER && $buyer->unit_type!=null) {
                                            return WorkLog::calculateOtherUnitsBetweenDates($startDate, $endDate);
                                        } else {
                                            return WorkLog::calculateTotalHoursBetweenDates($startDate, $endDate);
                                        }
                                    }),
                                Placeholder::make('worklog_units')
                                    ->hiddenLabel()
                                    ->content(function (callable $get) use ($buyer) {
                                        if ($buyer->contract_type === TypeOfContract::DAILY) {
                                            return __('invoices.days');
                                        } else if ($buyer->contract_type === TypeOfContract::OTHER && $buyer->unit_type!=null) {
                                            return $buyer->unit_type;
                                        } else {
                                            return __('invoices.hours');
                                        }
                                    }),
                                Placeholder::make('x')
                                    ->hiddenLabel()
                                    ->content("X"),
                                Placeholder::make('worklog_rate')
                                    ->hiddenLabel()
                                    ->content(number_format($buyer->contract_rate, 2, ',').' '.$buyer->currency->code),
                                Placeholder::make('=')
                                    ->hiddenLabel()
                                    ->content("="),
                                Placeholder::make('worklog_price')
                                    ->hiddenLabel()
                                    ->content(function (callable $get) use ($buyer) {
                                        $startDate = $get('start_date');
                                        $endDate = $get('end_date');

                                        if (!$startDate || !$endDate) {
                                            return '0.00';
                                        }
                                        $price = WorkLog::calculatePriceBasedOnTime($startDate, $endDate, $buyer->contract_type, $buyer->contract_rate);
                                        return number_format($price, 2, ',');
                                    }),
                                Placeholder::make('worklog_currency_code')
                                    ->hiddenLabel()
                                    ->content($buyer->currency->code),
                            ]),
                    ])
                    ->action(function (array $data, callable $set) use ($buyer) {
                        $startDate = $data['start_date'];
                        $endDate = $data['end_date'];

                        if (!$startDate || !$endDate) {
                            return;
                        }
                        if ($buyer->contract_type === TypeOfContract::DAILY) {
                            // Calculate the actual number of working days
                            $workingDays = WorkLog::calculateWorkingDaysBetweenDates($startDate, $endDate);
                            $set('items.0.quantity', $workingDays);
                            $set('items.0.price_net', $buyer->contract_rate);

                        }  else if ($buyer->contract_type === TypeOfContract::OTHER && $buyer->unit_type!=null) {
                            $units = WorkLog::calculateOtherUnitsBetweenDates($startDate, $endDate);
                            $set('items.0.quantity', $units);
                            $set('items.0.price_net', $buyer->contract_rate);
                        } else {
                            // For hourly contracts, set quantity to 1 and calculate total price
                            $totalPrice = WorkLog::calculatePriceBasedOnTime($startDate, $endDate, $buyer->contract_type, $buyer->contract_rate);
                            $set('items.0.quantity', 1);
                            $set('items.0.price_net', $totalPrice);
                        }
                        $set('items.0.name', "");
                        $set('items.0.discount', 0);
                    });
            }
        }
        return null;
    }
}
