<?php

namespace App\Http\Requests\Image;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg'],
        ];
    }
}
