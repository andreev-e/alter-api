<?php

namespace App\Http\Requests\Poi;

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
            'user' => ['sometimes', 'string', 'exists:users,username'],
            'location' => ['sometimes', 'string', 'exists:tags,url'],
            'route' => ['sometimes', 'numeric', 'exists:routes,id'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['sometimes', 'string'],
            'latest' => ['sometimes', 'boolean'],
        ];
    }
}
