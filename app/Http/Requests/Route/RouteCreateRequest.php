<?php

namespace App\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteCreateRequest extends FormRequest
{
    public function rules()
    {
        return array_merge(
            [
                'name' => ['required', 'string'],
                'description' => ['required', 'string'],
                'route' => ['sometimes', 'nullable', 'string'],
                'links' => ['sometimes', 'nullable', 'string'],
                'cost' => ['sometimes', 'nullable', 'integer'],
                'days' => ['sometimes', 'nullable', 'integer'],
                'encoded_route' => ['sometimes', 'nullable', 'string'],
                'start' => ['sometimes', 'nullable', 'string'],
                'finish' => ['sometimes', 'nullable', 'string'],
            ]
        );
    }
}
