<?php

namespace App\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'user' => ['sometimes', 'string', 'exists:users,username'],
        ];
    }
}
