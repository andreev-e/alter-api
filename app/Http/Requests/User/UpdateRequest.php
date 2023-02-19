<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'sometimes',
            'firstname' => 'sometimes|string',
            'lastname' => 'sometimes|string',
            'about' => 'sometimes|string',
            'homepage' => 'sometimes|string',
        ];
    }
}
