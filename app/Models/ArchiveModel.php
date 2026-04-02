<?php

namespace App\Models;

class ArchiveModel extends BaseModel
{
    protected string $table = 'archives';

    protected array $fillable = [
        'name',
        'code',
        'notes',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
}