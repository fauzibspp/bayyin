<?php

namespace App\Requests;

class StorePaymentRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|min:2|max:255',
            'notes' => 'required|min:2',
            'is_paid' => 'required',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'amount.required' => 'Amount is required.',
            'status.required' => 'Status is required.',
            'notes.required' => 'Notes is required.',
            'is_paid.required' => 'Is Paid is required.',
        ];
    }
}