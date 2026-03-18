<?php

namespace App\Models;

class CategoryModel extends BaseModel
{
    protected string $table = 'categories';

    protected array $fillable = [
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
    ];
}