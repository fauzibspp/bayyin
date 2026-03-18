<?php

namespace App\Requests;

class StoreInvoiceRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'amount' => 'required|numeric',
            'status' => 'required|min:2|max:255',
            'remarks' => 'required|min:2',
            'is_paid' => 'required',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'amount.required' => 'Amount is required.',
            'status.required' => 'Status is required.',
            'remarks.required' => 'Remarks is required.',
            'is_paid.required' => 'Is Paid is required.',
        ];
    }
}