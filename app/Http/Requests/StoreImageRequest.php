<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'image' => ['requires', 'image', 'mimes:jpg,jpeg'],
        ];
    }
}
