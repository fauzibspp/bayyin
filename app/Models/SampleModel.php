<?php

namespace App\Models;

class SampleModel extends BaseModel
{
    protected string $table = 'samples';

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