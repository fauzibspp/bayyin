<?php

namespace App\Requests;

class StoreCustomerRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|min:2|max:255',
            'phone' => 'required|min:2|max:255',
            'notes' => 'required|min:2',
            'is_active' => 'required',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'phone.required' => 'Phone is required.',
            'notes.required' => 'Notes is required.',
            'is_active.required' => 'Is Active is required.',
        ];
    }
}