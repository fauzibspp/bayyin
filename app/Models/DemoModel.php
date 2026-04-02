<?php

namespace App\Models;

class DemoModel extends BaseModel
{
    protected string $table = 'demos';

    protected array $fillable = [
        'name',
        'status',
        'notes',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}