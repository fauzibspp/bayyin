<?php

namespace App\Requests;

class StoreOrderRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'total' => 'required|numeric',
            'status' => 'required|min:2|max:255',
            'notes' => 'required|min:2',
            'is_paid' => 'required',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'total.required' => 'Total is required.',
            'status.required' => 'Status is required.',
            'notes.required' => 'Notes is required.',
            'is_paid.required' => 'Is Paid is required.',
        ];
    }
}