<?php

namespace App\Requests;

class StoreSampleRequest
{
    public static function rules(): array
    {
        return [
            'name' => 'required|min:2|max:255',
            'status' => 'required|min:2|max:255',
            'notes' => 'required|min:2',
            'is_active' => 'required',
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'status.required' => 'Status is required.',
            'notes.required' => 'Notes is required.',
            'is_active.required' => 'Is Active is required.',
        ];
    }
}