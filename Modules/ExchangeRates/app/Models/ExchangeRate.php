<?php

namespace Modules\ExchangeRates\Models;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// use Modules\ExchangeRates\Database\Factories\ExchangeRateFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['type', 'date', 'value', 'currency_id', 'base_currency_id', 'source'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'value' => 'float',
    ];


    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function baseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }


    protected static ?string $model = ExchangeRate::class;

    // protected static function newFactory(): ExchangeRateFactory
    // {
    //     // return ExchangeRateFactory::new();
    // }
}
