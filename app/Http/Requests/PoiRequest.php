<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PoiRequest extends FormRequest
{
    public function rules()
    {
        return [
            'south' => ['sometimes', 'number'],
            'west' => ['sometimes', 'number'],
            'north' => ['sometimes', 'number'],
            'east' => ['sometimes', 'number'],
            'tag' => ['sometimes', 'string'],
        ];
    }
}
