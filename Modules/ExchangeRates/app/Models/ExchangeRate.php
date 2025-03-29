<?php

namespace Modules\ExchangeRates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ExchangeRates\Database\Factories\ExchangeRateFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['type', 'date', 'value', 'currency', 'base_currency', 'source'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'value' => 'float',
    ];

    protected static ?string $model = ExchangeRate::class;

    // protected static function newFactory(): ExchangeRateFactory
    // {
    //     // return ExchangeRateFactory::new();
    // }
}
