<?php

namespace App\Models;

class CustomerModel extends BaseModel
{
    protected string $table = 'customers';

    protected array $fillable = [
        'name',
        'email',
        'phone',
        'notes',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}