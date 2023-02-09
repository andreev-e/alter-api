<?php

namespace App\Http\Requests\Poi;

class PoiCreateRequest extends PoiUpdateRequest
{
    public function rules()
    {
        return [
            ...parent::rules(),
            'lat' => ['required', 'numeric', 'min:-90', 'max:90'],
            'lng' => ['required', 'numeric', 'min:-180', 'max:180'],
        ];
    }
}
