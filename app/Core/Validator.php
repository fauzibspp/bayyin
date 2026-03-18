<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($rulesList as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        if ($rule === 'required') {
            if ($value === null || trim((string) $value) === '') {
                $this->errors[$field][] = ucfirst($field) . ' is required.';
            }
            return;
        }

        if ($rule === 'email') {
            if ($value !== null && trim((string) $value) !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field][] = ucfirst($field) . ' must be a valid email.';
            }
            return;
        }

        if ($rule === 'numeric') {
            if ($value !== null && trim((string) $value) !== '' && !is_numeric($value)) {
                $this->errors[$field][] = ucfirst($field) . ' must be numeric.';
            }
            return;
        }

        if (str_starts_with($rule, 'min:')) {
            $min = (int) explode(':', $rule, 2)[1];
            if (strlen((string) $value) < $min) {
                $this->errors[$field][] = ucfirst($field) . " must be at least {$min} characters.";
            }
            return;
        }

        if (str_starts_with($rule, 'max:')) {
            $max = (int) explode(':', $rule, 2)[1];
            if (strlen((string) $value) > $max) {
                $this->errors[$field][] = ucfirst($field) . " must not exceed {$max} characters.";
            }
            return;
        }

        if ($rule === 'alpha_dash') {
            if ($value !== null && trim((string) $value) !== '' && !preg_match('/^[a-zA-Z0-9_\-]+$/', (string) $value)) {
                $this->errors[$field][] = ucfirst($field) . ' may only contain letters, numbers, dash and underscore.';
            }
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}