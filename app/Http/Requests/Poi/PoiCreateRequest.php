<?php

namespace App\Http\Requests\Poi;

class PoiCreateRequest extends PoiUpdateRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'lat' => ['sometimes', 'numeric', 'min:-90', 'max:90'],
                'lng' => ['sometimes', 'numeric', 'min:-180', 'max:180'],
            ]
        );
    }
}
