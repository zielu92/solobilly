<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'quantity',
        'price_net',
        'price_gross',
        'tax_rate',
        'tax_amount',
        'discount',
        'discount_type',
        'total_net',
        'total_gross',
        'total_tax',
        'total_discount',
        'invoice_id',
        'display_units',
        'units'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'price_net' => 'decimal:2',
        'price_gross' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_net' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'invoice_id' => 'integer',
        'display_units' => 'boolean'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
