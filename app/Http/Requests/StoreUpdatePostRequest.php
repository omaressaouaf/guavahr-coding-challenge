<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdatePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string']
        ];
    }
}
