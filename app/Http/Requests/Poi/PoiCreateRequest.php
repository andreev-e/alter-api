<?php

namespace App\Http\Requests\Poi;

use Illuminate\Foundation\Http\FormRequest;

class PoiCreateRequest extends FormRequest
{
    public function rules()
    {
        return array_merge(
            [
                'description' => ['required', 'string'],
                'name' => ['required', 'nullable', 'string'],
                'name_en' => ['sometimes', 'string'],
                'type' => ['required', 'string'],
                'route' => ['sometimes', 'nullable', 'string'],
                'route_o' => ['sometimes', 'nullable', 'string'],
                'addon' => ['sometimes', 'nullable', 'string'],
                'links' => ['sometimes', 'nullable', 'string'],
                'ytb' => ['sometimes', 'nullable', 'string'],
                'copyright' => ['sometimes', 'nullable', 'string'],
                'lat' => ['required', 'numeric', 'min:-90', 'max:90'],
                'lng' => ['required', 'numeric', 'min:-180', 'max:180'],
                'tags' => ['sometimes', 'array'],
                'tags.*' => ['sometimes', 'integer', 'exists:tags,id'],
            ]
        );
    }
}
