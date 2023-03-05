<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QRRequest extends FormRequest
{
    public function rules()
    {
        return [
            'lat' => ['required', 'string'],
            'lng' => ['required', 'string'],
        ];
    }
}
