<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PoiRequest extends FormRequest
{
    public function rules()
    {
        return [
            'south' => ['sometimes', 'numeric'],
            'west' => ['sometimes', 'numeric'],
            'north' => ['sometimes', 'numeric'],
            'east' => ['sometimes', 'numeric'],
            'tag' => ['sometimes', 'string', 'exists:tags,url'],
            'location' => ['sometimes', 'string', 'exists:tags,url'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['sometimes', 'string'],
        ];
    }
}
