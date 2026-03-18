<?php

namespace App\Requests;

class StoreProductRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'is_active' => 'required',
            'description' => 'required|min:2',
        ];
    }
}