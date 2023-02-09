<?php

namespace App\Http\Requests\Poi;

use Illuminate\Foundation\Http\FormRequest;

class PoiUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'description' => ['required', 'string'],
            'name' => ['required', 'string'],
            'type' => ['required', 'string'],
            'route' => ['sometimes', 'string'],
            'route_o' => ['sometimes', 'string'],
            'addon' => ['sometimes', 'string'],
            'links' => ['sometimes', 'string'],
            'lat' => ['sometimes', 'numeric', 'min:-90', 'max:90'],
            'lng' => ['sometimes', 'numeric', 'min:-180', 'max:180'],
        ];
    }
}
