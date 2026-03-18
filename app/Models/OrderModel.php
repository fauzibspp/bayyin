<?php

namespace App\Models;

class OrderModel extends BaseModel
{
    protected string $table = 'orders';

    protected array $fillable = [
        'name',
        'total',
        'status',
        'notes',
        'is_paid',
        'created_at',
        'updated_at',
    ];
}