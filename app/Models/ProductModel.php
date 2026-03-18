<?php

namespace App\Models;

class ProductModel extends BaseModel
{
    protected string $table = 'products';

    protected array $fillable = [
        'name',
        'price',
        'stock',
        'is_active',
        'description',
        'created_at',
        'updated_at',
    ];
}