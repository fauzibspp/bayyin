<?php

namespace App\Core;

class SchemaParser
{
    public static function parse(?string $schema): array
    {
        if (!$schema) {
            return [
                [
                    'name' => 'name',
                    'type' => 'string',
                ]
            ];
        }

        $fields = [];
        $parts = array_filter(array_map('trim', explode(',', $schema)));

        foreach ($parts as $part) {
            $segments = array_map('trim', explode(':', $part));
            $name = $segments[0] ?? null;
            $type = strtolower($segments[1] ?? 'string');

            if (!$name) {
                continue;
            }

            $fields[] = [
                'name' => preg_replace('/[^A-Za-z0-9_]/', '', $name),
                'type' => $type,
            ];
        }

        return $fields ?: [
            [
                'name' => 'name',
                'type' => 'string',
            ]
        ];
    }

    public static function migrationType(string $type): string
    {
        return match ($type) {
            'string' => 'VARCHAR(255)',
            'text' => 'TEXT',
            'int', 'integer' => 'INT',
            'bigint' => 'BIGINT',
            'decimal' => 'DECIMAL(12,2)',
            'boolean', 'bool' => 'TINYINT(1)',
            'date' => 'DATE',
            'datetime' => 'DATETIME',
            default => 'VARCHAR(255)',
        };
    }

    public static function validationRule(string $type): string
    {
        return match ($type) {
            'string' => 'required|min:2|max:255',
            'text' => 'required|min:2',
            'int', 'integer', 'bigint' => 'required|numeric',
            'decimal' => 'required|numeric',
            'boolean', 'bool' => 'required',
            'date' => 'required',
            'datetime' => 'required',
            default => 'required',
        };
    }

    public static function inputType(string $type): string
    {
        return match ($type) {
            'int', 'integer', 'bigint' => 'number',
            'decimal' => 'number',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'boolean', 'bool' => 'checkbox',
            default => 'text',
        };
    }

    public static function isTextarea(string $type): bool
    {
        return $type === 'text';
    }

    public static function isCheckbox(string $type): bool
    {
        return in_array($type, ['boolean', 'bool'], true);
    }
}