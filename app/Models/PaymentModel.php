<?php

namespace App\Models;

class PaymentModel extends BaseModel
{
    protected string $table = 'payments';

    protected array $fillable = [
        'name',
        'amount',
        'status',
        'notes',
        'is_paid',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}