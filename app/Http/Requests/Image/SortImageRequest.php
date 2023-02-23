<?php

namespace App\Http\Requests\Image;

use Illuminate\Foundation\Http\FormRequest;

class SortImageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:media,id'],
        ];
    }
}
