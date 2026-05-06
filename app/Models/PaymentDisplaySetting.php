<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDisplaySetting extends Model
{
    protected $fillable = [
        'qris_image_path',
        'bank_name',
        'account_number',
        'account_holder',
        'instructions',
    ];
}
