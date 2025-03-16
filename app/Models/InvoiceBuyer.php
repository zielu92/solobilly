<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceBuyer extends Model
{
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
        'invoice_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
