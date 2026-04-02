<?php

namespace App\Core;

class GeneratorHelper
{
    public static function filteredFields(array $fields): array
    {
        return array_values(array_filter(
            $fields,
            fn(array $field) => ($field['name'] ?? '') !== 'deleted_at'
        ));
    }

    public static function hasDeletedAt(array $fields): bool
    {
        foreach ($fields as $field) {
            if (($field['name'] ?? '') === 'deleted_at') {
                return true;
            }
        }

        return false;
    }

    public static function buildFillableBlock(array $fields): string
    {
        $lines = [];

        foreach ($fields as $field) {
            $lines[] = "        '{$field['name']}',";
        }

        $lines[] = "        'created_at',";
        $lines[] = "        'updated_at',";

        return implode("\n", $lines);
    }

    public static function buildRulesBlock(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $rule = SchemaParser::validationRule($field['type']);
            $lines[] = "            '{$field['name']}' => '{$rule}',";
        }

        return implode("\n", $lines);
    }

    public static function buildMessagesBlock(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $lines[] = "            '{$field['name']}.required' => '{$label} is required.',";
        }

        return implode("\n", $lines);
    }

    public static function buildPostAssignments(array $fields, string $indent = '                '): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $lines[] = "{$indent}'{$field['name']}' => \$_POST['{$field['name']}'] ?? null,";
        }

        return implode("\n", $lines);
    }

    public static function buildJsonAssignments(array $fields, string $indent = '            '): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $lines[] = "{$indent}'{$field['name']}' => \$input['{$field['name']}'],";
        }

        return implode("\n", $lines);
    }

    public static function buildValidationChecks(array $fields, string $indent = '        '): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $lines[] = "{$indent}if (!array_key_exists('{$field['name']}', \$input) || \$input['{$field['name']}'] === '') {";
            $lines[] = "{$indent}    \$errors['{$field['name']}'][] = '{$label} is required.';";
            $lines[] = "{$indent}}";
        }

        return implode("\n", $lines);
    }

    public static function buildSearchableFieldsBlock(array $fields): string
    {
        $items = [];

        foreach (self::filteredFields($fields) as $field) {
            $items[] = "'{$field['name']}'";
        }

        return implode(', ', $items);
    }

    public static function buildExportFieldsBlock(array $fields): string
    {
        $items = ["'id'"];

        foreach ($fields as $field) {
            $items[] = "'{$field['name']}'";
        }

        return implode(', ', $items);
    }

    public static function buildColumnHeaders(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $lines[] = "                    <th>{$label}</th>";
        }

        return implode("\n", $lines);
    }

    public static function buildColumnsJs(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $lines[] = "                { data: '{$field['name']}' },";
        }

        return implode("\n", $lines);
    }

    public static function buildShowFields(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $name = $field['name'];

            $lines[] = "";
            $lines[] = "        <tr>";
            $lines[] = "            <th width=\"220\">{$label}</th>";
            $lines[] = "            <td><?= htmlspecialchars((string) (\$data['{$name}'] ?? '')) ?></td>";
            $lines[] = "        </tr>";
        }

        return implode("\n", $lines);
    }

    public static function buildModalCreateFields(array $fields): string
    {
        $output = '';

        foreach (self::filteredFields($fields) as $field) {
            $name = $field['name'];
            $label = ucwords(str_replace('_', ' ', $name));
            $type = $field['type'];

            if (SchemaParser::isTextarea($type)) {
                $output .= <<<PHP

                    <div class="form-group">
                        <label for="create_{$name}">{$label}</label>
                        <textarea name="{$name}" id="create_{$name}" class="form-control" rows="4" required></textarea>
                    </div>
PHP;
            } elseif (SchemaParser::isCheckbox($type)) {
                $output .= <<<PHP

                    <div class="form-group form-check">
                        <input type="hidden" name="{$name}" value="0">
                        <input type="checkbox" name="{$name}" id="create_{$name}" value="1" class="form-check-input">
                        <label class="form-check-label" for="create_{$name}">{$label}</label>
                    </div>
PHP;
            } else {
                $inputType = SchemaParser::inputType($type);
                $step = $type === 'decimal' ? ' step="0.01"' : '';

                $output .= <<<PHP

                    <div class="form-group">
                        <label for="create_{$name}">{$label}</label>
                        <input type="{$inputType}" name="{$name}" id="create_{$name}" class="form-control"{$step} required>
                    </div>
PHP;
            }
        }

        return $output;
    }

    public static function buildModalEditFields(array $fields): string
    {
        $output = '';

        foreach (self::filteredFields($fields) as $field) {
            $name = $field['name'];
            $label = ucwords(str_replace('_', ' ', $name));
            $type = $field['type'];

            if (SchemaParser::isTextarea($type)) {
                $output .= <<<PHP

                    <div class="form-group">
                        <label for="edit_{$name}">{$label}</label>
                        <textarea name="{$name}" id="edit_{$name}" class="form-control" rows="4" required></textarea>
                    </div>
PHP;
            } elseif (SchemaParser::isCheckbox($type)) {
                $output .= <<<PHP

                    <div class="form-group form-check">
                        <input type="hidden" name="{$name}" value="0">
                        <input type="checkbox" name="{$name}" id="edit_{$name}" value="1" class="form-check-input">
                        <label class="form-check-label" for="edit_{$name}">{$label}</label>
                    </div>
PHP;
            } else {
                $inputType = SchemaParser::inputType($type);
                $step = $type === 'decimal' ? ' step="0.01"' : '';

                $output .= <<<PHP

                    <div class="form-group">
                        <label for="edit_{$name}">{$label}</label>
                        <input type="{$inputType}" name="{$name}" id="edit_{$name}" class="form-control"{$step} required>
                    </div>
PHP;
            }
        }

        return $output;
    }

    public static function buildEditModalJsHydration(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $name = $field['name'];

            if (SchemaParser::isCheckbox($field['type'])) {
                $lines[] = "        $('#edit_{$name}').prop('checked', !!parseInt(row.{$name} || 0, 10));";
            } else {
                $lines[] = "        $('#edit_{$name}').val(row.{$name} || '');";
            }
        }

        return implode("\n", $lines);
    }

    public static function buildEditModalJsSerialize(array $fields): string
    {
        $lines = [];

        foreach (self::filteredFields($fields) as $field) {
            $name = $field['name'];

            if (SchemaParser::isCheckbox($field['type'])) {
                $lines[] = "        data['{$name}'] = $('#edit_{$name}').is(':checked') ? 1 : 0;";
            }
        }

        return implode("\n", $lines);
    }
}