<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PoiRequest extends FormRequest
{
    public function rules()
    {
        return [
            'bounds' => ['sometimes', 'string'],
        ];
    }
}
