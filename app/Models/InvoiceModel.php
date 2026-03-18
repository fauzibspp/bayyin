<?php

namespace App\Models;

class InvoiceModel extends BaseModel
{
    protected string $table = 'invoices';

    protected array $fillable = [
        'name',
        'amount',
        'status',
        'remarks',
        'is_paid',
        'created_at',
        'updated_at',
    ];
}