<?php

namespace Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ["accountNumber", "bankName", "iban", "swift", "beneficiaryName", "beneficiaryAddress", "user_id", "payment_method_id"];


}
