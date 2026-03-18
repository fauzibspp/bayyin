<?php

namespace App\Requests;

class StoreCategoryRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'description' => 'required|min:2',
            'is_active' => 'required',
        ];
    }
}